<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Utils\Respuesta;

class ProductoController extends Controller
{
    /**
     * Listar productos.
     */
    public function listado()
    {
        $categorias = Categoria::whereNull('deleted_at')
            ->where('estado', 'ACTIVO')
            ->orderBy('nombre')
            ->get();

        $marcas = Marca::whereNull('deleted_at')
            ->where('estado', 'ACTIVO')
            ->orderBy('nombre')
            ->get();

        return view('producto.listado', compact(
            'categorias',
            'marcas'
        ));
    }

    /**
     * Listado AJAX.
     */
    public function ajaxListado(Request $request)
    {
        if (!$request->ajax()) {
            return Respuesta::error(
                null,
                'Error al obtener los datos'
            );
        }

        $productos = Producto::with([
            'categoria',
            'marca'
        ])
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->get();

        $listado = view(
            'producto.ajaxListado',
            compact('productos')
        )->render();

        return response()->json([
            'estado' => true,
            'data' => [
                'listado' => $listado
            ]
        ]);
    }

    /**
     * Registrar o actualizar producto.
     */
    public function guardarProducto(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:100',
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|max:50',
            'categoria_id' => 'required|exists:categorias,id',
            'marca_id' => 'nullable|exists:marcas,id',
            'unidad_medida' => 'required|string|max:50',
            'cantidad' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'estado' => 'nullable|string|max:50',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

    

        if ($request->id) {

            $producto = Producto::whereNull('deleted_at')
                ->findOrFail($request->id);

            $producto->codigo = $request->codigo;
            $producto->nombre = $request->nombre;
            $producto->tipo = $request->tipo;
            $producto->categoria_id = $request->categoria_id;
            $producto->marca_id = $request->marca_id;
            $producto->unidad_medida = $request->unidad_medida;
            $producto->cantidad = $request->cantidad;
            $producto->stock_minimo = $request->stock_minimo;
            $producto->descripcion = $request->descripcion;

            if ($request->has('estado')) {
                $producto->estado = $request->estado;
            }

           

            if ($request->hasFile('imagen')) {

                $imagen = $request->file('imagen');

                $nombreImagen = time() . '_' . $imagen->getClientOriginalName();

                $imagen->move(
                    public_path('uploads/productos'),
                    $nombreImagen
                );

                $producto->imagen = 'uploads/productos/' . $nombreImagen;
            }

            $producto->usuario_modificador_id = Auth::id();

            $producto->save();

            return response()->json([
                'estado' => true,
                'mensaje' => 'Producto actualizado correctamente.',
                'data' => $producto
            ]);
        }

        

        $producto = new Producto();

        $producto->codigo = $request->codigo;
        $producto->nombre = $request->nombre;
        $producto->tipo = $request->tipo;
        $producto->categoria_id = $request->categoria_id;
        $producto->marca_id = $request->marca_id;
        $producto->unidad_medida = $request->unidad_medida;
        $producto->cantidad = $request->cantidad;
        $producto->stock_minimo = $request->stock_minimo;
        $producto->descripcion = $request->descripcion;
        $producto->estado = $request->estado ?? 'ACTIVO';
        $producto->usuario_creador_id = Auth::id();

     

        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen');
            $nombreImagen = time() . '_' . $imagen->getClientOriginalName();
            $imagen->move(
                public_path('uploads/productos'),
                $nombreImagen
            );

            $producto->imagen = 'uploads/productos/' . $nombreImagen;
        }

        $producto->save();

        return response()->json([
            'estado' => true,
            'mensaje' => 'Producto registrado correctamente.',
            'data' => $producto
        ]);
    }

    /**
     * Eliminar producto.
     */
    public function eliminarProducto(Request $request)
    {
        $producto = Producto::whereNull('deleted_at')
            ->findOrFail($request->id);

        $producto->usuario_eliminador_id = Auth::id();
        $producto->deleted_at = now();
        $producto->estado = 'INACTIVO';

        $producto->save();

        return response()->json([
            'estado' => true,
            'mensaje' => 'Producto eliminado correctamente.'
        ]);
    }
}