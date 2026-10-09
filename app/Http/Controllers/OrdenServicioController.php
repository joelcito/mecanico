<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\OrdenServicio;
use App\Models\Vehiculo;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\OrdenCotizacion;
use App\Models\Reparacion;

class OrdenServicioController extends Controller
{
    public function listado()
    {
        return view('ordenServicio.listado');
    }

    public function ajaxListado(Request $request)
{
    if (!$request->ajax()) {
        return response()->json(
            Respuesta::error(
                null,
                'Solicitud no válida'
            )->toArray(),
            400
        );
    }

    $ordenes = OrdenServicio::with([
        'vehiculo.cliente.user',
        'vehiculo.marca',
    ])
        ->orderByDesc('id')
        ->get();

    $listado = view(
        'ordenServicio.ajaxListado',
        compact('ordenes')
    )->render();

    return response()->json(
        Respuesta::success(
            [
                'listado' => $listado
            ],
            'Listado cargado correctamente'
        )->toArray()
    );
}
    public function nuevo()
    {
        $vehiculos = Vehiculo::with([
            'cliente.user',
            'marca'
        ])
            ->where('estado', 'ACTIVO')
            ->orderBy('id', 'desc')
            ->get();

        return view(
            'ordenServicio.nuevo',
            compact('vehiculos')
        );
    }

   
    public function guardar(Request $request)
    {
        $request->validate([
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'fecha_recepcion' => 'required|date',
            'kilometraje' => 'nullable|integer|min:0',
            'motivo_ingreso' => 'nullable|string|max:255',
            'nivel_combustible' => 'nullable|numeric|min:0|max:100',
            'observaciones' => 'nullable|string',
        ]);

        try {

            DB::beginTransaction();
            $ultimaOrden = OrdenServicio::withTrashed()
                ->orderByDesc('id')
                ->first();
            $siguienteNumero = $ultimaOrden
                ? $ultimaOrden->id + 1
                : 1;

            $numeroOrden = 'OR-' . str_pad(
                $siguienteNumero,
                6,
                '0',
                STR_PAD_LEFT
            );

            $orden = OrdenServicio::create([
                'numero_orden' => $numeroOrden,
                'vehiculo_id' => $request->vehiculo_id,
                'fecha_recepcion' => $request->fecha_recepcion,
                'kilometraje' => $request->kilometraje,
                'motivo_ingreso' => $request->motivo_ingreso,
                'nivel_combustible' => $request->nivel_combustible,
                'observaciones' => $request->observaciones,
                'estado' => 'RECIBIDO',
                'usuario_creador_id' => Auth::id(),
            ]);

            DB::commit();
            return response()->json(
                Respuesta::success(
                    $orden,
                    'Orden de servicio registrada correctamente'
                )->toArray()
            );

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(
                Respuesta::error(
                    null,
                    'No se pudo registrar la orden: ' . $e->getMessage()
                )->toArray(),
                500
            );
        }
    }

    public function detalle($id)
    {
        $orden = OrdenServicio::with([
            'vehiculo.cliente.user',
            'vehiculo.marca',
            'inspeccionActual.detalles',
            'inspeccionActual.fotos',
            'diagnosticoActual.detalles',
            'cotizacionActual.detalles.producto',
            'reparacion.tecnico',
            'reparacion.detalles.producto',
            'reparacion.movimientosInventario.producto',
            'reparacion.movimientosInventario.usuarioCreador',
            'reparacion.avances.usuario',
            'reparacion.avances.tecnico',
            'reparacion.asignacionesHerramientas.herramienta.marca',
            'reparacion.asignacionesHerramientas.mecanico',
            'entrega.usuario',
        ])->findOrFail($id);

        return view(
            'ordenServicio.detalle',
            compact('orden')
        );
    }

    public function aprobarCotizacion($id)
    {
        try {
            DB::beginTransaction();
            $cotizacion = \App\Models\OrdenCotizacion::where(
                'orden_servicio_id',
                $id
            )
                ->latest('id')
                ->first();

            if (!$cotizacion) {
                throw new \Exception(
                    'La orden no tiene una cotización registrada.'
                );
            }
            if ($cotizacion->estado !== 'PENDIENTE') {
                throw new \Exception(
                    'La cotización ya fue respondida. Estado actual: ' .
                    $cotizacion->estado
                );
            }
            $cotizacion->update([
                'estado' => 'APROBADA',
                'fecha_respuesta' => now(),
                'usuario_respuesta_id' => Auth::id(),
                'usuario_modificador_id' => Auth::id(),
            ]);

            $orden = OrdenServicio::findOrFail($id);
            $orden->update([
                'estado' => 'AUTORIZADO',
                'usuario_modificador_id' => Auth::id(),
            ]);

            DB::commit();
            return response()->json(
                Respuesta::success(
                    $cotizacion,
                    'Cotización aprobada correctamente'
                )->toArray()
            );

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(
                Respuesta::error(
                    null,
                    'No se pudo aprobar la cotización: ' . $e->getMessage()
                )->toArray(),
                500
            );
        }
    }

    public function rechazarCotizacion(Request $request, $id)
    {
        $request->validate([
            'observacion_respuesta' => 'required|string|max:1000',
        ]);
        try {
            DB::beginTransaction();
            $cotizacion = \App\Models\OrdenCotizacion::where(
                'orden_servicio_id',
                $id
            )
                ->where('estado', 'PENDIENTE')
                ->latest('id')
                ->firstOrFail();

            $cotizacion->update([
                'estado' => 'RECHAZADA',
                'fecha_respuesta' => now(),
                'usuario_respuesta_id' => Auth::id(),
                'observacion_respuesta' => $request->observacion_respuesta,
                'usuario_modificador_id' => Auth::id(),
            ]);

            $orden = OrdenServicio::findOrFail($id);
            $orden->update([
                'estado' => 'EN_COTIZACION',
                'usuario_modificador_id' => Auth::id(),
            ]);
            DB::commit();
            return response()->json(
                Respuesta::success(
                    $cotizacion,
                    'Cotización rechazada correctamente'
                )->toArray()
            );

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(
                Respuesta::error(
                    null,
                    'No se pudo rechazar la cotización: ' . $e->getMessage()
                )->toArray(),
                500
            );
        }
    }

    public function iniciarReparacion($id)
        {
            try {
                DB::beginTransaction();
                $orden = OrdenServicio::findOrFail($id);
                if ($orden->estado !== 'AUTORIZADO') {
                    return response()->json([
                        'estado' => false,
                        'message' => 'La orden debe estar autorizada para iniciar la reparación.'
                    ], 422);
                }
                $reparacion = Reparacion::where(
                    'orden_servicio_id',
                    $orden->id
                )->first();
                if (!$reparacion) {
                    $reparacion = Reparacion::create([
                        'orden_servicio_id' => $orden->id,
                        'fecha_inicio' => now(),
                        'estado' => 'EN_PROCESO',
                        'usuario_creador_id' => Auth::id(),
                    ]);
                } else {
                    $reparacion->update([
                        'fecha_inicio' => $reparacion->fecha_inicio ?? now(),
                        'estado' => 'EN_PROCESO',
                        'usuario_modificador_id' => Auth::id(),
                    ]);
                }

                $orden->update([
                    'estado' => 'EN_REPARACION',
                    'usuario_modificador_id' => Auth::id(),
                ]);

                DB::commit();
                return response()->json([
                    'estado' => true,
                    'message' => 'La reparación fue iniciada correctamente.',
                    'data' => $reparacion
                ]);

            } catch (\Throwable $e) {
                DB::rollBack();
                return response()->json([
                    'estado' => false,
                    'message' => 'No se pudo iniciar la reparación: ' . $e->getMessage()
                ], 500);
            }
        }


    public function guardarReparacion(Request $request, $id)
    {
        $request->validate([
            'tecnico_id' => 'nullable|exists:users,id',
            'fecha_inicio_reparacion' => 'nullable|date',
            'trabajos_realizados' => 'nullable|string',
            'observaciones_reparacion' => 'nullable|string',
        ]);

        try {
            $orden = OrdenServicio::findOrFail($id);
            if ($orden->estado !== 'EN_REPARACION') {
                return response()->json([
                    'estado' => false,
                    'message' => 'La orden no se encuentra en proceso de reparación.'
                ], 422);
            }

            $reparacion = Reparacion::where(
                'orden_servicio_id',
                $orden->id
            )->first();

            if (!$reparacion) {
                return response()->json([
                    'estado' => false,
                    'message' => 'No existe una reparación iniciada para esta orden.'
                ], 422);
            }

            $reparacion->update([
                'tecnico_id' => $request->tecnico_id,
                'fecha_inicio' => $request->fecha_inicio_reparacion
                    ? $request->fecha_inicio_reparacion . ' 00:00:00'
                    : $reparacion->fecha_inicio,
                'trabajos_realizados' => $request->trabajos_realizados,
                'observaciones' => $request->observaciones_reparacion,
                'usuario_modificador_id' => Auth::id(),
            ]);

            return response()->json([
                'estado' => true,
                'message' => 'Avance de reparación guardado correctamente.',
                'data' => $reparacion
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'estado' => false,
                'message' => 'No se pudo guardar la reparación: ' . $e->getMessage()
            ], 500);
        }
    }


    public function finalizarReparacion($id)
    {
        try {
            DB::beginTransaction();
            $orden = OrdenServicio::findOrFail($id);
            if ($orden->estado !== 'EN_REPARACION') {
                return response()->json([
                    'estado' => false,
                    'message' => 'La orden no se encuentra en proceso de reparación.'
                ], 422);
            }
            $reparacion = Reparacion::where(
                'orden_servicio_id',
                $orden->id
            )->first();

            if (!$reparacion) {
                return response()->json([
                    'estado' => false,
                    'message' => 'No existe una reparación registrada para esta orden.'
                ], 422);
            }

            if (!$reparacion->trabajos_realizados) {
                return response()->json([
                    'estado' => false,
                    'message' => 'Debe registrar los trabajos realizados antes de finalizar la reparación.'
                ], 422);
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

            DB::commit();

            return response()->json([
                'estado' => true,
                'message' => 'La reparación fue finalizada correctamente. El vehículo está listo para la entrega.',
                'data' => $reparacion
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'estado' => false,
                'message' => 'No se pudo finalizar la reparación: ' . $e->getMessage()
            ], 500);
        }
    }

}