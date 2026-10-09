<?php

namespace App\Http\Controllers;

use App\Models\OrdenServicio;
use App\Models\MovimientoInventario;
use App\Models\Reparacion;
use App\Models\ReparacionDetalle;
use App\Models\ReparacionAvance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class ReparacionController extends Controller
{
    public function datos($id)
    {
        try {

            $orden = OrdenServicio::with([
                'reparacion.tecnico',
            ])->findOrFail($id);

            $tecnicos = User::where('estado', 'ACTIVO')
                ->where('rol_id', 2)
                ->orderBy('nombres')
                ->get();

            return response()->json([
                'estado' => true,
                'data' => [
                    'reparacion' => $orden->reparacion,
                    'tecnicos' => $tecnicos,
                ]
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'estado' => false,
                'message' => 'No se pudieron cargar los datos de reparación.'
            ], 500);
        }
    }

    public function guardar(Request $request, $id)
    {
        try {
            $request->validate([
                'tecnico_id' => [
                    'nullable',
                    Rule::exists('users', 'id')
                        ->where('estado', 'ACTIVO')
                        ->where('rol_id', 2),
                ],
                'trabajos_realizados' => 'nullable|string',
                'observaciones' => 'nullable|string',
            ]);

            [$orden, $reparacion] = DB::transaction(function () use ($request, $id) {
                $orden = OrdenServicio::lockForUpdate()->findOrFail($id);
                if ($orden->estado !== 'EN_REPARACION') {
                    throw ValidationException::withMessages([
                        'orden' => 'La orden no se encuentra en reparación.',
                    ]);
                }

                $reparacion = Reparacion::where('orden_servicio_id', $orden->id)
                    ->lockForUpdate()
                    ->firstOrNew(['orden_servicio_id' => $orden->id]);
                if ($reparacion->exists && $reparacion->estado === 'FINALIZADA') {
                    throw ValidationException::withMessages([
                        'reparacion' => 'No se puede modificar una reparación finalizada.',
                    ]);
                }

                if (!$reparacion->exists) {
                    $reparacion->orden_servicio_id = $orden->id;
                    $reparacion->fecha_inicio = now();
                    $reparacion->estado = 'EN_PROCESO';
                    $reparacion->usuario_creador_id = Auth::id();
                } else {
                    $reparacion->usuario_modificador_id = Auth::id();
                }

                $reparacion->tecnico_id = $request->tecnico_id;
                $reparacion->trabajos_realizados = $request->trabajos_realizados;
                $reparacion->observaciones = $request->observaciones;
                $reparacion->save();

                return [$orden, $reparacion];
            });

            return response()->json([
                'estado' => true,
                'message' => 'La información de reparación fue guardada correctamente.',
                'data' => $reparacion->load('tecnico')
            ]);

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {

            return response()->json([
                'estado' => false,
                'message' => 'No se pudo guardar la reparación. La operación fue revertida.'
            ], 500);
        }
    }

    public function finalizar($id)
    {
        [$orden, $reparacion] = DB::transaction(function () use ($id) {
            $orden = OrdenServicio::lockForUpdate()->findOrFail($id);
            if ($orden->estado !== 'EN_REPARACION') {
                throw ValidationException::withMessages([
                    'orden' => 'La orden no se encuentra en reparación.',
                ]);
            }
            $reparacion = Reparacion::where('orden_servicio_id', $orden->id)
                ->lockForUpdate()
                ->first();

            if (!$reparacion) {
                throw ValidationException::withMessages([
                    'reparacion' => 'Debe registrar la información de la reparación antes de finalizarla.',
                ]);
            }

            if (!$reparacion->trabajos_realizados) {
                throw ValidationException::withMessages([
                    'trabajos_realizados' => 'Debe registrar los trabajos realizados antes de finalizar la reparación.',
                ]);
            }

            if (!$reparacion->tecnico_id) {
                throw ValidationException::withMessages([
                    'tecnico_id' => 'Asigne un técnico antes de finalizar la reparación.',
                ]);
            }

            $detalles = ReparacionDetalle::withTrashed()
                ->where('reparacion_id', $reparacion->id)
                ->lockForUpdate()
                ->get();
            $idsDetalle = $detalles->pluck('id');

            $movimientosSinDetalle = MovimientoInventario::withTrashed()
                ->where('reparacion_id', $reparacion->id)
                ->whereIn('motivo', ['REPARACION', 'DEVOLUCION_REPARACION'])
                ->where(function ($query) use ($idsDetalle) {
                    $query->whereNull('referencia_tipo')
                        ->orWhere('referencia_tipo', '!=', ReparacionDetalle::class)
                        ->orWhereNull('referencia_id')
                        ->orWhereNotIn('referencia_id', $idsDetalle);
                })
                ->exists();

            if ($movimientosSinDetalle) {
                throw ValidationException::withMessages([
                    'productos' => 'Hay movimientos de reparación sin un detalle asociado.',
                ]);
            }

            foreach ($detalles as $detalle) {
                $movimientos = MovimientoInventario::withTrashed()
                    ->where('reparacion_id', $reparacion->id)
                    ->where('referencia_tipo', ReparacionDetalle::class)
                    ->where('referencia_id', $detalle->id)
                    ->get();
                $netoUtilizado = 0.0;

                foreach ($movimientos as $movimiento) {
                    $motivoEsperado = $movimiento->tipo === 'SALIDA'
                        ? 'REPARACION'
                        : 'DEVOLUCION_REPARACION';
                    if ((int) $movimiento->producto_id !== (int) $detalle->producto_id
                        || (int) $movimiento->orden_servicio_id !== (int) $orden->id
                        || !in_array($movimiento->tipo, ['ENTRADA', 'SALIDA'], true)
                        || $movimiento->motivo !== $motivoEsperado
                        || $movimiento->trashed()) {
                        throw ValidationException::withMessages([
                            'productos' => 'Un movimiento de inventario tiene datos inconsistentes con la reparación.',
                        ]);
                    }

                    $netoUtilizado += $movimiento->tipo === 'SALIDA'
                        ? (float) $movimiento->cantidad
                        : -(float) $movimiento->cantidad;
                }

                $cantidadEsperada = $detalle->trashed()
                    ? 0.0
                    : (float) $detalle->cantidad;

                if (abs(round($netoUtilizado - $cantidadEsperada, 2)) >= 0.01) {
                    throw ValidationException::withMessages([
                        'productos' => "El historial de inventario no coincide con la cantidad del producto {$detalle->producto_id}.",
                    ]);
                }
            }

            $reparacion->update([
                'fecha_fin' => now(),
                'estado' => 'FINALIZADA',
                'usuario_modificador_id' => Auth::id(),
            ]);

            $orden->update([
                'estado' => 'LISTO',
                'usuario_modificador_id' => Auth::id(),
            ]);

            return [$orden, $reparacion];
        });

        return response()->json([
            'estado' => true,
            'message' => 'La reparación fue finalizada correctamente.',
            'data' => [
                'reparacion' => $reparacion,
                'orden' => $orden,
            ],
        ]);
    }

    public function avances($id)
    {
        $orden = OrdenServicio::with('reparacion.avances.usuario', 'reparacion.avances.tecnico')
            ->findOrFail($id);

        return response()->json([
            'estado' => true,
            'data' => $orden->reparacion?->avances ?? collect(),
        ]);
    }

    public function guardarAvance(Request $request, $id)
    {
        $datos = $request->validate([
            'descripcion' => 'required|string|max:5000',
            'observaciones' => 'nullable|string|max:5000',
        ]);

        $avance = DB::transaction(function () use ($id, $datos) {
            $orden = OrdenServicio::lockForUpdate()->findOrFail($id);
            if ($orden->estado !== 'EN_REPARACION') {
                abort(422, 'La orden debe estar EN_REPARACION.');
            }

            $reparacion = Reparacion::where('orden_servicio_id', $orden->id)
                ->lockForUpdate()
                ->firstOrFail();
            if ($reparacion->estado === 'FINALIZADA') {
                abort(422, 'La reparación ya fue finalizada y no puede modificarse.');
            }

            return ReparacionAvance::create([
                'reparacion_id' => $reparacion->id,
                'usuario_id' => Auth::id(),
                'tecnico_id' => $reparacion->tecnico_id,
                'fecha' => now(),
                'descripcion' => $datos['descripcion'],
                'observaciones' => $datos['observaciones'] ?? null,
            ])->load(['usuario', 'tecnico']);
        });

        return response()->json([
            'estado' => true,
            'message' => 'Avance registrado.',
            'data' => $avance,
        ], 201);
    }
}