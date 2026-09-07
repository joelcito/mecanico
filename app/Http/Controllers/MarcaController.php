<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Utils\Respuesta;

class MarcaController extends Controller
{
    /**
     * Listar marcas.
     */
    public function listado()
    {
        return view('marca.listado');
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

        $marcas = Marca::whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->get();

        $listado = view(
            'marca.ajaxListado',
            compact('marcas')
        )->render();

        return response()->json([
            'estado' => true,
            'data' => [
                'listado' => $listado
            ]
        ]);
    }

    /**
     * Registrar o actualizar marca.
     */
    public function guardarMarca(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'estado' => 'nullable|string|max:50',
        ]);

        

        if ($request->id) {

            $marca = Marca::whereNull('deleted_at')
                ->findOrFail($request->id);

            $marca->nombre = $request->nombre;
            $marca->descripcion = $request->descripcion;

            if ($request->has('estado')) {
                $marca->estado = $request->estado;
            }

            $marca->usuario_modificador_id = Auth::id();

            $marca->save();

            return response()->json([
                'estado' => true,
                'mensaje' => 'Marca actualizada correctamente.',
                'data' => $marca
            ]);
        }

        

        $marca = new Marca();

        $marca->nombre = $request->nombre;
        $marca->descripcion = $request->descripcion;
        $marca->estado = $request->estado ?? 'ACTIVO';
        $marca->usuario_creador_id = Auth::id();

        $marca->save();

        return response()->json([
            'estado' => true,
            'mensaje' => 'Marca registrada correctamente.',
            'data' => $marca
        ]);
    }

    /**
     * Eliminar marca.
     */
    public function eliminarMarca(Request $request)
    {
        $marca = Marca::whereNull('deleted_at')
            ->findOrFail($request->id);

        $marca->usuario_eliminador_id = Auth::id();
        $marca->deleted_at = now();
        $marca->estado = 'INACTIVO';

        $marca->save();

        return response()->json([
            'estado' => true,
            'mensaje' => 'Marca eliminada correctamente.'
        ]);
    }
}