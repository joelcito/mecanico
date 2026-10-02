<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Utils\Respuesta;

class CategoriaController extends Controller
{
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
    'tipo' => 'required|in:PRODUCTO,HERRAMIENTA',
    'estado' => 'nullable|string|max:50',
]);
        if ($request->id) {
            $categoria = Categoria::whereNull('deleted_at')->findOrFail($request->id);

            if (
                $categoria->tipo !== $request->tipo &&
                (($request->tipo === 'HERRAMIENTA' && $categoria->productos()->whereNull('deleted_at')->exists()) ||
                    ($request->tipo === 'PRODUCTO' && $categoria->herramientas()->whereNull('deleted_at')->exists()))
            ) {
                return response()->json([
                    'estado' => false,
                    'message' => 'No se puede cambiar el tipo de una categoría con registros activos asociados.',
                ], 422);
            }

            $categoria->nombre = $request->nombre;
            $categoria->descripcion = $request->descripcion;
            $categoria->tipo = $request->tipo;
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

        $categoria = new Categoria();
        $categoria->nombre = $request->nombre;
        $categoria->descripcion = $request->descripcion;
        $categoria->tipo = $request->tipo;
        $categoria->estado = $request->estado ?? 'ACTIVO';
        $categoria->usuario_creador_id = Auth::id();
        $categoria->save();
        return response()->json([
            'estado' => true,
            'mensaje' => 'Categoría registrada correctamente.',
            'data' => $categoria
        ]);
    }

    public function eliminarCategoria(Request $request)
    {
        $categoria = Categoria::whereNull('deleted_at')->findOrFail($request->id);
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