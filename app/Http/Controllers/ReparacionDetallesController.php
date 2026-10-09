<?php

namespace App\Http\Controllers;

use App\Models\OrdenCotizacion;
use App\Models\OrdenServicio;
use App\Models\Producto;
use App\Models\Reparacion;
use App\Models\ReparacionDetalle;
use App\Models\AsignacionHerramienta;
use App\Models\Herramienta;
use App\Services\InventarioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReparacionDetallesController extends Controller
{
    public function productosDisponibles(Request $request, $ordenId)
    {
        $orden = OrdenServicio::findOrFail($ordenId);
        if ($orden->estado !== 'EN_REPARACION') {
            abort(422, 'La orden no se encuentra en reparación.');
        }

        $productos = Producto::with(['categoria', 'marca'])
            ->whereNull('deleted_at')
            ->where('estado', 'ACTIVO')
            ->whereHas('categoria', function ($query) {
                $query->where('tipo', 'PRODUCTO')->whereNull('deleted_at');
            })
            ->where('cantidad', '>', 0)
            ->when($request->filled('q'), function ($query) use ($request) {
                $termino = trim($request->input('q'));
                $query->where(function ($query) use ($termino) {
                    $query->where('nombre', 'like', "%{$termino}%")
                        ->orWhere('codigo', 'like', "%{$termino}%")
                        ->orWhereHas('marca', function ($marcaQuery) use ($termino) {
                            $marcaQuery->where('nombre', 'like', "%{$termino}%");
                        });
                });
            })
            ->orderBy('nombre')
            ->limit(30)
            ->get(['id', 'codigo', 'nombre', 'marca_id', 'unidad_medida', 'cantidad']);

        return response()->json([
            'estado' => true,
            'data' => $productos,
        ]);
    }

    public function herramientasDisponibles($ordenId)
    {
        $orden = OrdenServicio::with('reparacion')->findOrFail($ordenId);
        abort_unless($orden->reparacion, 404, 'No existe reparación para esta orden.');

        $herramientas = Herramienta::with('marca')
            ->whereNull('deleted_at')
            ->where('estado', 'ACTIVO')
            ->withSum([
                'asignacionesHerramientas as cantidad_asignada' => function ($query) {
                    $query->where('estado', 'ASIGNADA');
                },
            ], 'cantidad')
            ->orderBy('nombre')
            ->get(['id', 'codigo', 'nombre', 'marca_id', 'unidad_medida', 'cantidad'])
            ->map(function (Herramienta $herramienta) {
                $herramienta->cantidad_disponible = max(
                    0,
                    (float) $herramienta->cantidad - (float) ($herramienta->cantidad_asignada ?? 0)
                );
                return $herramienta;
            });

        return response()->json(['estado' => true, 'data' => $herramientas]);
    }

    public function asignacionesHerramientas($ordenId)
    {
        $orden = OrdenServicio::with('reparacion')->findOrFail($ordenId);
        abort_unless($orden->reparacion, 404, 'No existe reparación para esta orden.');

        $asignaciones = $orden->reparacion->asignacionesHerramientas()
            ->with(['herramienta.marca', 'mecanico'])
            ->orderByDesc('fecha_asignacion')
            ->get();

        return response()->json(['estado' => true, 'data' => $asignaciones]);
    }

    public function asignarHerramienta(Request $request, $ordenId)
    {
        $datos = $request->validate([
            'herramienta_id' => 'required|integer|exists:herramientas,id',
            'cantidad' => 'required|numeric|gt:0',
            'observaciones' => 'nullable|string|max:2000',
        ]);

        $asignacion = DB::transaction(function () use ($ordenId, $datos) {
            $orden = OrdenServicio::lockForUpdate()->findOrFail($ordenId);
            $this->validarOrdenEditable($orden);
            $reparacion = Reparacion::where('orden_servicio_id', $orden->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (!$reparacion->tecnico_id) {
                abort(422, 'Asigne un técnico a la reparación antes de prestar herramientas.');
            }

            $herramienta = Herramienta::whereNull('deleted_at')
                ->lockForUpdate()
                ->findOrFail($datos['herramienta_id']);
            if ($herramienta->estado !== 'ACTIVO') {
                abort(422, 'La herramienta no está activa.');
            }

            $asignadas = (float) AsignacionHerramienta::where('herramienta_id', $herramienta->id)
                ->where('estado', 'ASIGNADA')
                ->sum('cantidad');
            $disponible = max(0, (float) $herramienta->cantidad - $asignadas);
            $cantidad = (float) $datos['cantidad'];
            if ($cantidad > $disponible) {
                abort(422, "Herramientas insuficientes. Disponibles: {$disponible}; solicitadas: {$cantidad}.");
            }

            return AsignacionHerramienta::create([
                'producto_id' => null,
                'herramienta_id' => $herramienta->id,
                'reparacion_id' => $reparacion->id,
                'mecanico_id' => $reparacion->tecnico_id,
                'cantidad' => $cantidad,
                'fecha_asignacion' => now(),
                'estado' => 'ASIGNADA',
                'observaciones' => $datos['observaciones'] ?? null,
                'usuario_creador_id' => Auth::id(),
            ])->load(['herramienta.marca', 'mecanico']);
        });

        return response()->json([
            'estado' => true,
            'message' => 'Herramienta prestada al técnico.',
            'data' => $asignacion,
        ], 201);
    }

    public function devolverHerramienta($ordenId, $asignacionId)
    {
        $asignacion = DB::transaction(function () use ($ordenId, $asignacionId) {
            $orden = OrdenServicio::lockForUpdate()->findOrFail($ordenId);
            $this->validarOrdenEditable($orden);
            $reparacion = Reparacion::where('orden_servicio_id', $orden->id)
                ->lockForUpdate()
                ->firstOrFail();
            $asignacion = $reparacion->asignacionesHerramientas()
                ->lockForUpdate()
                ->findOrFail($asignacionId);

            if ($asignacion->estado !== 'ASIGNADA') {
                abort(422, 'Esta herramienta ya fue devuelta.');
            }

            $asignacion->update([
                'estado' => 'DEVUELTA',
                'fecha_devolucion' => now(),
                'usuario_modificador_id' => Auth::id(),
            ]);

            return $asignacion->load(['herramienta.marca', 'mecanico']);
        });

        return response()->json([
            'estado' => true,
            'message' => 'Herramienta devuelta.',
            'data' => $asignacion,
        ]);
    }

    public function index($ordenId)
    {
        $orden = OrdenServicio::findOrFail($ordenId);
        $reparacion = Reparacion::where('orden_servicio_id', $orden->id)->first();

        return response()->json([
            'estado' => true,
            'data' => [
                'detalles' => $reparacion
                    ? $reparacion->detalles()->with('producto')->orderBy('id')->get()
                    : [],
                'movimientos' => $reparacion
                    ? $reparacion->movimientosInventario()
                        ->with(['producto', 'usuarioCreador'])
                        ->whereIn('motivo', ['REPARACION', 'DEVOLUCION_REPARACION'])
                        ->orderByDesc('fecha')
                        ->get()
                    : [],
            ],
        ]);
    }

    public function store(Request $request, $ordenId, InventarioService $inventario)
    {
        $datos = $request->validate([
            'producto_id' => 'required|integer|exists:productos,id',
            'cantidad' => 'required|numeric|gt:0',
            'tipo_uso' => 'nullable|string|max:30',
            'observaciones' => 'nullable|string',
        ]);

        $detalle = DB::transaction(function () use ($ordenId, $datos, $inventario) {
            $orden = OrdenServicio::lockForUpdate()->findOrFail($ordenId);
            $this->validarOrdenEditable($orden);
            $reparacion = Reparacion::where('orden_servicio_id', $orden->id)
                ->lockForUpdate()
                ->firstOrFail();
            $productoId = (int) $datos['producto_id'];
            $cantidadSolicitada = (float) $datos['cantidad'];
            $producto = Producto::with('categoria')->findOrFail($productoId);

            if ($producto->estado !== 'ACTIVO'
                || $producto->deleted_at !== null
                || $producto->categoria?->tipo !== 'PRODUCTO') {
                abort(422, 'Seleccione un producto activo del catálogo de productos.');
            }

            $detalle = ReparacionDetalle::where('reparacion_id', $reparacion->id)
                ->where('producto_id', $productoId)
                ->lockForUpdate()
                ->first();

            if ($detalle) {
                $diferencia = $cantidadSolicitada;
                $cantidadFinal = (float) $detalle->cantidad + $diferencia;
                $detalle->cantidad = $cantidadFinal;
                $detalle->tipo_uso = $datos['tipo_uso'] ?? $detalle->tipo_uso;
                $detalle->observaciones = $datos['observaciones'] ?? $detalle->observaciones;
                $detalle->usuario_modificador_id = Auth::id();
                $detalle->save();
            } else {
                $detalleCotizacion = $this->detalleCotizacionAprobada($orden, $productoId);
                $detalle = ReparacionDetalle::create([
                    'reparacion_id' => $reparacion->id,
                    'producto_id' => $productoId,
                    'orden_cotizacion_detalle_id' => $detalleCotizacion?->id,
                    'cantidad' => $cantidadSolicitada,
                    'tipo_uso' => $datos['tipo_uso'] ?? 'REPUESTO',
                    'origen' => 'TALLER',
                    'observaciones' => $datos['observaciones'] ?? null,
                    'usuario_creador_id' => Auth::id(),
                ]);
            }

            $inventario->registrarSalida($productoId, $cantidadSolicitada, [
                'orden_servicio_id' => $orden->id,
                'reparacion_id' => $reparacion->id,
                'referencia_tipo' => ReparacionDetalle::class,
                'referencia_id' => $detalle->id,
                'observaciones' => $datos['observaciones'] ?? null,
            ]);

            return $detalle->load('producto', 'detalleCotizacion');
        });

        return response()->json([
            'estado' => true,
            'message' => 'Producto agregado a la reparación y descontado del inventario.',
            'data' => $detalle,
        ], 201);
    }

    public function update(Request $request, $ordenId, $detalleId, InventarioService $inventario)
    {
        $datos = $request->validate([
            'cantidad' => 'required|numeric|gt:0',
            'observaciones' => 'nullable|string',
        ]);

        $detalle = DB::transaction(function () use ($ordenId, $detalleId, $datos, $inventario) {
            $orden = OrdenServicio::lockForUpdate()->findOrFail($ordenId);
            $this->validarOrdenEditable($orden);
            $reparacion = Reparacion::where('orden_servicio_id', $orden->id)
                ->lockForUpdate()
                ->firstOrFail();
            $detalle = ReparacionDetalle::where('reparacion_id', $reparacion->id)
                ->lockForUpdate()
                ->findOrFail($detalleId);

            $cantidadAnterior = (float) $detalle->cantidad;
            $cantidadNueva = (float) $datos['cantidad'];
            $diferencia = round($cantidadNueva - $cantidadAnterior, 2);
            $contexto = [
                'orden_servicio_id' => $orden->id,
                'reparacion_id' => $reparacion->id,
                'referencia_tipo' => ReparacionDetalle::class,
                'referencia_id' => $detalle->id,
                'observaciones' => $datos['observaciones'] ?? 'Ajuste de cantidad utilizada',
            ];

            if ($diferencia > 0) {
                $inventario->registrarSalida((int) $detalle->producto_id, $diferencia, $contexto);
            } elseif ($diferencia < 0) {
                $inventario->registrarDevolucion((int) $detalle->producto_id, abs($diferencia), $contexto);
            }

            $detalle->cantidad = $cantidadNueva;
            if (array_key_exists('observaciones', $datos)) {
                $detalle->observaciones = $datos['observaciones'];
            }
            $detalle->usuario_modificador_id = Auth::id();
            $detalle->save();

            return $detalle->load('producto');
        });

        return response()->json([
            'estado' => true,
            'message' => 'Cantidad utilizada actualizada y movimientos registrados.',
            'data' => $detalle,
        ]);
    }

    public function destroy($ordenId, $detalleId, InventarioService $inventario)
    {
        DB::transaction(function () use ($ordenId, $detalleId, $inventario) {
            $orden = OrdenServicio::lockForUpdate()->findOrFail($ordenId);
            $this->validarOrdenEditable($orden);
            $reparacion = Reparacion::where('orden_servicio_id', $orden->id)
                ->lockForUpdate()
                ->firstOrFail();
            $detalle = ReparacionDetalle::where('reparacion_id', $reparacion->id)
                ->lockForUpdate()
                ->findOrFail($detalleId);

            $inventario->registrarDevolucion((int) $detalle->producto_id, (float) $detalle->cantidad, [
                'orden_servicio_id' => $orden->id,
                'reparacion_id' => $reparacion->id,
                'referencia_tipo' => ReparacionDetalle::class,
                'referencia_id' => $detalle->id,
                'observaciones' => 'Devolución por eliminación del detalle de reparación',
            ]);

            $detalle->usuario_eliminador_id = Auth::id();
            $detalle->save();
            $detalle->delete();
        });

        return response()->json([
            'estado' => true,
            'message' => 'Producto eliminado del detalle y devuelto al inventario.',
        ]);
    }

    private function validarOrdenEditable(OrdenServicio $orden): void
    {
        if ($orden->estado !== 'EN_REPARACION') {
            abort(422, 'Solo se pueden modificar productos cuando la orden está EN_REPARACION.');
        }

        if (Reparacion::where('orden_servicio_id', $orden->id)
            ->where('estado', 'FINALIZADA')
            ->exists()) {
            abort(422, 'No se puede modificar una reparación finalizada.');
        }
    }

    private function detalleCotizacionAprobada(OrdenServicio $orden, int $productoId)
    {
        $cotizacion = OrdenCotizacion::where('orden_servicio_id', $orden->id)
            ->where('estado', 'APROBADA')
            ->latest('id')
            ->first();

        if (!$cotizacion) {
            return null;
        }

        $detalles = $cotizacion->detalles()
            ->where('tipo', 'PRODUCTO')
            ->where('producto_id', $productoId)
            ->get();

        return $detalles->count() === 1 ? $detalles->first() : null;
    }
}
