<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Utils\Respuesta;

class CategoriaController extends Controller
{
    /**
     * Listar categorías.
     */
   public function listado()
{
    return view('categoria.listado');
}

public function ajaxListado(Request $request)
    {
        if (!$request->ajax()) {
            return Respuesta::error(
                null,
                'Error al obtener los datos'
            );
        }

        $categorias = Categoria::whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->get();

        $listado = view(
            'categoria.ajaxListado',
            compact('categorias')
        )->render();

        return response()->json([
            'estado' => true,
            'data' => [
                'listado' => $listado
            ]
        ]);
    }



    public function guardarCategoria(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'nullable|string|max:50',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Actualizar
        |--------------------------------------------------------------------------
        */

        if ($request->id) {

            $categoria = Categoria::whereNull('deleted_at')
                ->findOrFail($request->id);

            $categoria->nombre = $request->nombre;
            $categoria->descripcion = $request->descripcion;

            if ($request->has('estado')) {
                $categoria->estado = $request->estado;
            }

            $categoria->usuario_modificador_id = Auth::id();

            $categoria->save();

            return response()->json([
                'estado' => true,
                'mensaje' => 'Categoría actualizada correctamente.',
                'data' => $categoria
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Registrar
        |--------------------------------------------------------------------------
        */

        $categoria = new Categoria();

        $categoria->nombre = $request->nombre;
        $categoria->descripcion = $request->descripcion;
        $categoria->estado = $request->estado ?? 'ACTIVO';
        $categoria->usuario_creador_id = Auth::id();

        $categoria->save();

        return response()->json([
            'estado' => true,
            'mensaje' => 'Categoría registrada correctamente.',
            'data' => $categoria
        ]);
    }

    /**
     * Eliminar categoría.
     */
    public function eliminarCategoria(Request $request)
    {
        $categoria = Categoria::whereNull('deleted_at')
            ->findOrFail($request->id);

        $categoria->usuario_eliminador_id = Auth::id();
        $categoria->deleted_at = now();
        $categoria->estado = 'INACTIVO';

        $categoria->save();

        return response()->json([
            'estado' => true,
            'mensaje' => 'Categoría eliminada correctamente.'
        ]);
    }

}