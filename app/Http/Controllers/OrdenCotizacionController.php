<?php

namespace App\Http\Controllers;

use App\Models\OrdenServicio;
use App\Models\OrdenCotizacion;
use App\Models\OrdenCotizacionDetalle;
use App\Models\Producto;
use App\Models\Categoria;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrdenCotizacionController extends Controller
{
    public function crear($id)
    {
        $orden = OrdenServicio::with([
            'vehiculo.cliente.user',
            'vehiculo.marca',
            'diagnosticoActual.detalles.fotos',
            'cotizacionActual.detalles.producto',
        ])->findOrFail($id);

        if ($orden->estado !== 'EN_COTIZACION') {
            return redirect()
                ->route('ordenServicio.detalle', $orden->id)
                ->with('error', 'La orden no se encuentra disponible para cotización.');
        }
        $cotizacion = $orden->cotizacionActual;
        if ($cotizacion && $cotizacion->estado === 'APROBADA') {
            return redirect()
                ->route('ordenServicio.detalle', $orden->id)
                ->with('error', 'No se puede editar una cotización aprobada.');
        }
        $productosCotizados = $cotizacion
            ? $cotizacion->detalles
                ->where('tipo', 'PRODUCTO')
                ->pluck('producto_id')
                ->filter()
                ->all()
            : [];
        $productos = Producto::where('estado', 'ACTIVO')
            ->whereNull('deleted_at')
            ->whereHas('categoria', function ($query) {
                $query->where('tipo', 'PRODUCTO')
                    ->whereNull('deleted_at');
            })
            ->where(function ($query) use ($productosCotizados) {
                $query->where('cantidad', '>', 0);

                if (!empty($productosCotizados)) {
                    $query->orWhereIn('id', $productosCotizados);
                }
            })
            ->orderBy('nombre')
            ->get();

        return view(
            'ordenServicio.cotizacion',
            compact('orden', 'productos')
        );
    }

    public function guardar(Request $request, $id)
{
    $categoriasProducto = Categoria::whereNull('deleted_at')
        ->where('tipo', 'PRODUCTO')
        ->pluck('id');

    $request->validate([
        'observaciones' => 'nullable|string',
        'detalles' => 'required|array|min:1',
        'detalles.*.id' => 'nullable|integer',
        'descuento' => 'nullable|numeric|min:0',
        'detalles.*.tipo' => 'required|in:PRODUCTO,SERVICIO',
        'detalles.*.producto_id' => [
            'nullable',
            Rule::exists('productos', 'id')
                ->whereNull('deleted_at')
                ->whereIn('categoria_id', $categoriasProducto->all()),
        ],
        'detalles.*.descripcion' => 'required|string|max:255',
        'detalles.*.cantidad' => 'required|numeric|min:0.01',
        'detalles.*.precio_unitario' => 'required|numeric|min:0',
    ]);

    DB::beginTransaction();

    try {
        $orden = OrdenServicio::whereKey($id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($orden->estado !== 'EN_COTIZACION') {
            throw new \Exception(
                'La orden no se encuentra disponible para cotización.'
            );
        }

        $cotizacion = OrdenCotizacion::where('orden_servicio_id', $orden->id)
            ->latest('id')
            ->lockForUpdate()
            ->first();

        if ($cotizacion && $cotizacion->estado === 'APROBADA') {
            throw new \Exception(
                'No se puede modificar una cotización aprobada.'
            );
        }

        $subtotal = 0;

        foreach ($request->detalles as $detalle) {
            $cantidad = (float) $detalle['cantidad'];
            $precio = (float) $detalle['precio_unitario'];

            $subtotal += $cantidad * $precio;
        }

        $descuento = (float) $request->input('descuento', 0);

        if ($descuento > $subtotal) {
            throw new \Exception(
                'El descuento no puede ser mayor al subtotal.'
            );
        }

        $total = $subtotal - $descuento;

        if (!$cotizacion) {
            $cotizacion = OrdenCotizacion::create([
                'orden_servicio_id' => $orden->id,
                'usuario_cotizador_id' => Auth::id(),
                'fecha' => now(),
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'total' => $total,
                'estado' => 'PENDIENTE',
                'observaciones' => $request->observaciones,
                'usuario_creador_id' => Auth::id(),
            ]);
        } else {
            $cotizacion->update([
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'total' => $total,
                'observaciones' => $request->observaciones,
                'usuario_modificador_id' => Auth::id(),
            ]);
        }

        $idsRecibidos = [];

        foreach ($request->detalles as $detalle) {
            $tipo = $detalle['tipo'];
            $productoId = $tipo === 'PRODUCTO'
                ? ($detalle['producto_id'] ?? null)
                : null;

            if ($tipo === 'PRODUCTO' && !$productoId) {
                throw new \Exception(
                    'Selecciona un producto en cada detalle de tipo Producto.'
                );
            }

            $descripcion = trim($detalle['descripcion']);

            // Para productos, usamos el nombre vigente del producto.
            if ($tipo === 'PRODUCTO') {
                $producto = Producto::whereKey($productoId)
                    ->where('estado', 'ACTIVO')
                    ->whereNull('deleted_at')
                    ->whereHas('categoria', function ($query) {
                        $query->where('tipo', 'PRODUCTO')
                            ->whereNull('deleted_at');
                    })
                    ->first();

                if (!$producto) {
                    throw new \Exception(
                        'Uno de los productos seleccionados ya no está disponible.'
                    );
                }

                $descripcion = $producto->nombre;
            }

            $datosDetalle = [
                'tipo' => $tipo,
                'producto_id' => $productoId,
                'descripcion' => $descripcion,
                'cantidad' => (float) $detalle['cantidad'],
                'precio_unitario' => (float) $detalle['precio_unitario'],
                'subtotal' => (float) $detalle['cantidad']
                    * (float) $detalle['precio_unitario'],
                'estado' => 'ACTIVO',
                'usuario_modificador_id' => Auth::id(),
            ];

            if (!empty($detalle['id'])) {
                // Impide actualizar detalles pertenecientes a otra cotización.
                $detalleExistente = OrdenCotizacionDetalle::whereKey($detalle['id'])
                    ->where('orden_cotizacion_id', $cotizacion->id)
                    ->first();

                if (!$detalleExistente) {
                    throw new \Exception(
                        'Uno de los detalles enviados no pertenece a esta cotización.'
                    );
                }

                $detalleExistente->update($datosDetalle);
                $idsRecibidos[] = $detalleExistente->id;
            } else {
                $datosDetalle['orden_cotizacion_id'] = $cotizacion->id;
                $datosDetalle['usuario_creador_id'] = Auth::id();

                $nuevoDetalle = OrdenCotizacionDetalle::create($datosDetalle);
                $idsRecibidos[] = $nuevoDetalle->id;
            }
        }

        // Retira los detalles que el usuario quitó del formulario.
        $detallesAntiguos = OrdenCotizacionDetalle::where(
            'orden_cotizacion_id',
            $cotizacion->id
        )->get();

        foreach ($detallesAntiguos as $detalleAntiguo) {
            if (!in_array($detalleAntiguo->id, $idsRecibidos)) {
                $detalleAntiguo->delete();
            }
        }

        $orden->update([
            'usuario_modificador_id' => Auth::id(),
        ]);

        DB::commit();

        return redirect()
            ->route('ordenServicio.detalle', $orden->id)
            ->with(
                'success',
                $cotizacion->wasRecentlyCreated
                    ? 'Cotización registrada correctamente.'
                    : 'Cotización actualizada correctamente.'
            );
    } catch (\Throwable $e) {
        DB::rollBack();

        report($e);

        return back()
            ->withInput()
            ->with(
                'error',
                'No se pudo guardar la cotización: ' . $e->getMessage()
            );
    }
}


public function pdf($id)
{
    $orden = OrdenServicio::with([
        'vehiculo.cliente.user',
        'vehiculo.marca',
        'diagnosticoActual.detalles.fotos',
        'cotizacionActual.detalles.producto',
    ])->findOrFail($id);

    $cotizacion = $orden->cotizacionActual;

    if (!$cotizacion) {
        return redirect()
            ->route('ordenServicio.detalle', $orden->id)
            ->with('error', 'Esta orden todavía no tiene una cotización.');
    }

    $pdf = Pdf::loadView(
        'ordenServicio.cotizacionPdf',
        compact('orden', 'cotizacion')
    )->setPaper('a4', 'portrait');

    return $pdf->stream(
        'cotizacion-orden-' . $orden->id . '.pdf'
    );
}

}
