<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\MovimientoCaja;
use App\Models\OrdenServicio;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PagoController extends Controller
{

 public function listado()
    {
        return view('pagos.listado');
    }

    public function ajaxListado(Request $request)
    {
        try {
            $pagos = Pago::with([
                'ordenServicio.vehiculo',
                'caja',
                'sucursal',
                'usuarioCreador',
            ])
                ->orderByDesc('id')
                ->get();

            $listado = view(
                'pagos.ajaxListado',
                compact('pagos')
            )->render();

            return response()->json([
                'estado' => true,
                'data' => [
                    'listado' => $listado
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'estado' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

   public function crear()
{
    try {

        $ordenes = OrdenServicio::with([
            'vehiculo',
            'cotizacionActual',
        ])
            ->orderByDesc('id')
            ->get();

        $ordenesDisponibles = $ordenes->filter(function ($orden) {
        $cotizacion = $orden->cotizacionActual;

            if ($orden->estado === 'ENTREGADO') {
                return false;
            }

            if (!$cotizacion) {
                return false;
            }

            if ($cotizacion->estado !== 'APROBADA') {
                return false;
            }

            $total = (float) $cotizacion->total;
            $pagado = (float) $orden->pagos()
                ->where('estado', 'ACTIVO')
                ->sum('monto');
            $saldo = $total - $pagado;
            return $saldo > 0;
        })->map(function ($orden) {
            $cotizacion = $orden->cotizacionActual;
            $total = (float) $cotizacion->total;
            $pagado = (float) $orden->pagos()
                ->where('estado', 'ACTIVO')
                ->sum('monto');

            $saldo = max(0, $total - $pagado);
            return [
                'id' => $orden->id,
                'numero_orden' => $orden->numero_orden,
                'vehiculo' => $orden->vehiculo ? [
                    'id' => $orden->vehiculo->id,
                    'placa' => $orden->vehiculo->placa ?? '',
                ] : null,
                'total' => $total,
                'pagado' => $pagado,
                'saldo' => $saldo,
            ];

        })->values();

        $cajas = Caja::with([
            'usuario',
            'sucursal'
        ])
            ->where('estado', 'ABIERTA')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'estado' => true,
            'ordenes' => $ordenesDisponibles,
            'cajas' => $cajas,
        ]);

    } catch (\Throwable $e) {

        return response()->json([
            'estado' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
    
  public function guardar(Request $request)
    {
        $request->validate([
            'orden_servicio_id' => [
                'required',
                'exists:orden_servicios,id'
            ],
            'caja_id' => [
                'required',
                'exists:cajas,id'
            ],
            'tipo_pago' => [
                'required',
                'in:EFECTIVO,QR,TRANSFERENCIA'
            ],
           
            'descripcion' => [
                'nullable',
                'string'
            ],
            'monto_recibido' => [
                'required',
                'numeric',
                'min:0'
            ],
        ], [
            'orden_servicio_id.required' => 'Debe seleccionar una orden de servicio.',
            'orden_servicio_id.exists' => 'La orden seleccionada no existe.',
            'caja_id.required' => 'Debe seleccionar una caja.',
            'caja_id.exists' => 'La caja seleccionada no existe.',
            'tipo_pago.required' => 'Debe seleccionar el método de pago.',
            'tipo_pago.in' => 'El método de pago no es válido.',
            'cambio.numeric' => 'El cambio debe ser numérico.',
            'cambio.min' => 'El cambio no puede ser negativo.',
        ]);

        try {
            $data = DB::transaction(function () use ($request) {
                $orden = OrdenServicio::with('cotizacionActual')
                    ->lockForUpdate()
                    ->findOrFail($request->orden_servicio_id);

                if ($orden->estado === 'ENTREGADO') {
                    throw ValidationException::withMessages([
                        'orden_servicio_id' => 'No se pueden registrar pagos en una orden ya entregada.',
                    ]);
                }
                if (!$orden->cotizacionActual) {
                    throw ValidationException::withMessages([
                        'orden_servicio_id' => 'La orden no tiene una cotización registrada.',
                    ]);
                }
                if ($orden->cotizacionActual->estado !== 'APROBADA') {
                    throw ValidationException::withMessages([
                        'orden_servicio_id' => 'La cotización debe estar aprobada para registrar pagos.',
                    ]);
                }

                $caja = Caja::lockForUpdate()->findOrFail($request->caja_id);
                if ($caja->estado !== 'ABIERTA') {
                    throw ValidationException::withMessages([
                        'caja_id' => 'La caja seleccionada no está abierta.',
                    ]);
                }

                $total = (float) $orden->cotizacionActual->total;
                $pagado = (float) $orden->pagos()->where('estado', 'ACTIVO')->sum('monto');
                $saldo = max(0, $total - $pagado);
                $montoRecibido = (float) $request->monto_recibido;
                if ($montoRecibido <= 0) {
                    throw ValidationException::withMessages([
                        'monto_recibido' => 'El monto recibido debe ser mayor a cero.',
                    ]);
                }
                if ($saldo <= 0) {
                    throw ValidationException::withMessages([
                        'orden_servicio_id' => 'La orden no tiene saldo pendiente.',
                    ]);
                }

                $monto = min($montoRecibido, $saldo);
                $cambio = $request->tipo_pago === 'EFECTIVO'
                    ? max(0, $montoRecibido - $monto)
                    : 0;
                $usuarioId = Auth::id();
                $pago = Pago::create([
                    'orden_servicio_id' => $orden->id,
                    'caja_id' => $caja->id,
                    'sucursal_id' => $caja->sucursal_id,
                    'monto' => $monto,
                    'monto_recibido' => $montoRecibido,
                    'cambio' => $cambio,
                    'fecha' => now(),
                    'tipo_pago' => $request->tipo_pago,
                    'descripcion' => $request->descripcion,
                    'estado' => 'ACTIVO',
                    'usuario_creador_id' => $usuarioId,
                ]);

                MovimientoCaja::create([
                    'caja_id' => $caja->id,
                    'pago_id' => $pago->id,
                    'tipo' => 'INGRESO',
                    'metodo_pago' => $request->tipo_pago,
                    'monto' => $monto,
                    'origen_dinero' => 'PAGO_ORDEN',
                    'descripcion' => 'Pago de Orden #' . $orden->numero_orden,
                    'fecha' => now(),
                    'estado' => 'ACTIVO',
                    'usuario_creador_id' => $usuarioId,
                ]);
                $caja->increment('total_ingresos', $monto);

                $nuevoPagado = $pagado + $monto;
                $nuevoSaldo = max(0, $total - $nuevoPagado);

                return [
                    'pago' => $pago,
                    'total' => $total,
                    'pagado' => $nuevoPagado,
                    'saldo' => $nuevoSaldo,
                    'estado_pago' => $nuevoPagado <= 0 ? 'SIN_PAGO' : ($nuevoSaldo > 0 ? 'PARCIAL' : 'PAGADO'),
                ];
            });

            return response()->json([
                'estado' => true,
                'message' => 'Pago registrado correctamente.',
                'data' => $data,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'estado' => false,
                'message' => collect($e->errors())->flatten()->first(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'estado' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function actual($id)
    {
        $pago = Pago::with([
            'ordenServicio.vehiculo',
            'caja.usuario',
            'caja.sucursal',
            'sucursal',
            'usuarioCreador',
            'movimientoCaja',
        ])->findOrFail($id);

        return view('pagos.actual', compact('pago'));
    }

    public function anular($id)
    {
        $ordenId = Pago::whereKey($id)->value('orden_servicio_id');
        if (!$ordenId) {
            return response()->json([
                'estado' => false,
                'message' => 'El pago no existe.',
            ], 404);
        }

        try {
            DB::transaction(function () use ($id, $ordenId) {
                $orden = OrdenServicio::lockForUpdate()->findOrFail($ordenId);
                if ($orden->estado === 'ENTREGADO') {
                    throw ValidationException::withMessages([
                        'pago' => 'No se puede anular un pago de una orden ya entregada.',
                    ]);
                }

                $pago = Pago::with('movimientoCaja')
                    ->lockForUpdate()
                    ->findOrFail($id);
                if ($pago->estado !== 'ACTIVO') {
                    throw ValidationException::withMessages([
                        'pago' => 'El pago ya se encuentra anulado.',
                    ]);
                }
                $caja = $pago->caja_id
                    ? Caja::lockForUpdate()->find($pago->caja_id)
                    : null;
                if (!$caja) {
                    throw ValidationException::withMessages([
                        'caja' => 'La caja asociada al pago no existe.',
                    ]);
                }

                if ($caja->estado !== 'ABIERTA') {
                    throw ValidationException::withMessages([
                        'caja' => 'No se puede anular un pago de una caja cerrada.',
                    ]);
                }

                $usuarioId = Auth::id();
                $pago->update([
                    'estado' => 'ANULADO',
                    'usuario_modificador_id' => $usuarioId,
                ]);
                if ($pago->movimientoCaja) {
                    $pago->movimientoCaja->update([
                        'estado' => 'ANULADO',
                        'usuario_modificador_id' => $usuarioId,
                    ]);
                }
                $caja->decrement('total_ingresos', $pago->monto);
            });

            return response()->json([
                'estado' => true,
                'message' => 'El pago fue anulado correctamente.'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'estado' => false,
                'message' => collect($e->errors())->flatten()->first(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'estado' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}