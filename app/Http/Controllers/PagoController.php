<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\MovimientoCaja;
use App\Models\OrdenServicio;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{

 public function listado()
    {
        return view('pagos.listado');
    }

    /**
     * Carga el listado vía AJAX.
     */
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

        $ordenesDisponibles = $ordenes->map(function ($orden) {

            $cotizacion = $orden->cotizacionActual;

            if (!$cotizacion) {
                return null;
            }

            if ($cotizacion->estado !== 'APROBADA') {
                return null;
            }

            $total = (float) $cotizacion->total;

            $pagado = (float) $orden->pagos()
                ->where('estado', 'ACTIVO')
                ->sum('monto');

            $saldo = max(0, $total - $pagado);

            if ($saldo <= 0) {
                return null;
            }

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

        })->filter()->values();


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
            'monto' => [
                'required',
                'numeric',
                'min:0.01'
            ],
            'cambio' => [
                'nullable',
                'numeric',
                'min:0'
            ],
            'descripcion' => [
                'nullable',
                'string'
            ],
        ], [
            'orden_servicio_id.required' => 'Debe seleccionar una orden de servicio.',
            'orden_servicio_id.exists' => 'La orden seleccionada no existe.',

            'caja_id.required' => 'Debe seleccionar una caja.',
            'caja_id.exists' => 'La caja seleccionada no existe.',

            'tipo_pago.required' => 'Debe seleccionar el método de pago.',
            'tipo_pago.in' => 'El método de pago no es válido.',

            'monto.required' => 'Debe ingresar el monto.',
            'monto.numeric' => 'El monto debe ser numérico.',
            'monto.min' => 'El monto debe ser mayor a 0.',

            'cambio.numeric' => 'El cambio debe ser numérico.',
            'cambio.min' => 'El cambio no puede ser negativo.',
        ]);

        $orden = OrdenServicio::with([
            'cotizacionActual'
        ])->findOrFail($request->orden_servicio_id);

        if (!$orden->cotizacionActual) {
            return response()->json([
                'estado' => false,
                'message' => 'La orden no tiene una cotización registrada.'
            ], 422);
        }

        if ($orden->cotizacionActual->estado !== 'APROBADA') {
            return response()->json([
                'estado' => false,
                'message' => 'La cotización debe estar aprobada para registrar pagos.'
            ], 422);
        }

        $caja = Caja::findOrFail($request->caja_id);

        if ($caja->estado !== 'ABIERTA') {
            return response()->json([
                'estado' => false,
                'message' => 'La caja seleccionada no está abierta.'
            ], 422);
        }

        $total = (float) $orden->cotizacionActual->total;

        $pagado = (float) $orden->pagos()
            ->where('estado', 'ACTIVO')
            ->sum('monto');

        $saldo = max(0, $total - $pagado);

        $monto = (float) $request->monto;
        $cambio = (float) ($request->cambio ?? 0);

        if ($monto > $saldo) {
            return response()->json([
                'estado' => false,
                'message' => 'El monto del pago supera el saldo pendiente.',
                'data' => [
                    'total' => $total,
                    'pagado' => $pagado,
                    'saldo' => $saldo
                ]
            ], 422);
        }

        /*
         * El cambio solo corresponde a efectivo.
         */
        if ($request->tipo_pago !== 'EFECTIVO') {
            $cambio = 0;
        }

        DB::beginTransaction();

        try {

            $usuarioId = Auth::id();

            $pago = Pago::create([
                'orden_servicio_id' => $orden->id,
                'caja_id' => $caja->id,
                'sucursal_id' => $caja->sucursal_id,
                'monto' => $monto,
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

            DB::commit();

            $nuevoPagado = $pagado + $monto;
            $nuevoSaldo = max(0, $total - $nuevoPagado);

            if ($nuevoPagado <= 0) {
                $estadoPago = 'SIN_PAGO';
            } elseif ($nuevoPagado < $total) {
                $estadoPago = 'PARCIAL';
            } else {
                $estadoPago = 'PAGADO';
            }

            return response()->json([
                'estado' => true,
                'message' => 'Pago registrado correctamente.',
                'data' => [
                    'pago' => $pago,
                    'total' => $total,
                    'pagado' => $nuevoPagado,
                    'saldo' => $nuevoSaldo,
                    'estado_pago' => $estadoPago,
                ]
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'estado' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ver un pago.
     */
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

    /**
     * Anular un pago.
     */
    public function anular($id)
    {
        $pago = Pago::with([
            'movimientoCaja',
            'caja'
        ])->findOrFail($id);

        if ($pago->estado !== 'ACTIVO') {
            return response()->json([
                'estado' => false,
                'message' => 'El pago ya se encuentra anulado.'
            ], 422);
        }

        if (!$pago->caja) {
            return response()->json([
                'estado' => false,
                'message' => 'La caja asociada al pago no existe.'
            ], 422);
        }

        $caja = $pago->caja;

        if ($caja->estado !== 'ABIERTA') {
            return response()->json([
                'estado' => false,
                'message' => 'No se puede anular un pago de una caja cerrada.'
            ], 422);
        }

        DB::beginTransaction();

        try {

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

            DB::commit();

            return response()->json([
                'estado' => true,
                'message' => 'El pago fue anulado correctamente.'
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'estado' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}