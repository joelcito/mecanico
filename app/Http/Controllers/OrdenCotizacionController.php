<?php

namespace App\Http\Controllers;

use App\Models\OrdenServicio;
use App\Models\OrdenCotizacion;
use App\Models\OrdenCotizacionDetalle;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrdenCotizacionController extends Controller
{
    /**
     * Mostrar formulario de cotización.
     */
    public function crear($id)
    {
        $orden = OrdenServicio::with([
            'vehiculo.cliente.user',
            'vehiculo.marca',
            'diagnosticoActual.detalles',
            'cotizacionActual.detalles.producto',
        ])->findOrFail($id);

        if ($orden->estado !== 'EN_COTIZACION') {
            return redirect()
                ->route('ordenServicio.detalle', $orden->id)
                ->with(
                    'error',
                    'La orden no se encuentra disponible para cotización.'
                );
        }

        $productos = Producto::where('estado', 'ACTIVO')
            ->where('tipo', 'PRODUCTO')
            ->where('cantidad', '>', 0)
            ->orderBy('nombre')
            ->get();

        return view(
            'ordenServicio.cotizacion',
            compact('orden', 'productos')
        );
    }

    /**
     * Guardar cotización.
     */
    public function guardar(Request $request, $id)
    {
        $request->validate([
            'observaciones' => 'nullable|string',

            'detalles' => 'required|array|min:1',

            'detalles.*.tipo' =>
                'required|in:PRODUCTO,SERVICIO',

            'detalles.*.producto_id' =>
                'nullable|exists:productos,id',

            'detalles.*.descripcion' =>
                'required|string|max:255',

            'detalles.*.cantidad' =>
                'required|numeric|min:0.01',

            'detalles.*.precio_unitario' =>
                'required|numeric|min:0',

            'detalles.*.descuento' =>
                'nullable|numeric|min:0',
        ]);

        try {

            DB::beginTransaction();

            $orden = OrdenServicio::findOrFail($id);

            if ($orden->estado !== 'EN_COTIZACION') {
                throw new \Exception(
                    'La orden no se encuentra disponible para cotización.'
                );
            }

            /*
             * Calculamos los totales.
             */
            $subtotal = 0;

            foreach ($request->detalles as $detalle) {

                $cantidad = (float) $detalle['cantidad'];
                $precio = (float) $detalle['precio_unitario'];

                $subtotalDetalle = $cantidad * $precio;

                $subtotal += $subtotalDetalle;
            }

            $descuento = 0;

            $total = $subtotal - $descuento;

            /*
             * Crear cabecera.
             */
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

            /*
             * Crear detalles.
             */
            foreach ($request->detalles as $detalle) {

                $cantidad = (float) $detalle['cantidad'];
                $precio = (float) $detalle['precio_unitario'];

                $subtotalDetalle = $cantidad * $precio;

                OrdenCotizacionDetalle::create([
                    'orden_cotizacion_id' => $cotizacion->id,
                    'tipo' => $detalle['tipo'],
                    'producto_id' =>
                        $detalle['tipo'] === 'PRODUCTO'
                            ? ($detalle['producto_id'] ?? null)
                            : null,
                    'descripcion' => $detalle['descripcion'],
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio,
                    'subtotal' => $subtotalDetalle,
                    'estado' => 'ACTIVO',
                    'usuario_creador_id' => Auth::id(),
                ]);
            }

            /*
             * La orden continúa en EN_COTIZACION
             * hasta que el cliente autorice.
             */
            $orden->update([
                'usuario_modificador_id' => Auth::id(),
            ]);

            DB::commit();

            return redirect()
                ->route('ordenServicio.detalle', $orden->id)
                ->with(
                    'success',
                    'Cotización registrada correctamente.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with(
                    'error',
                    'No se pudo registrar la cotización: ' .
                    $e->getMessage()
                );
        }
    }
}