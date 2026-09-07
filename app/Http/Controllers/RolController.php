<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RolController extends Controller
{
    public function listado()
    {
        return view('rol.listado');
    }

    public function ajaxListado(Request $request)
    {
        if ($request->ajax()) {
            $roles = Rol::all();
            $valores = [
                'listado' => view('rol.ajaxListado')->with(compact('roles'))->render()
            ];

            $data = Respuesta::success($valores, "Datos Obtenidos correctamente");

        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function guardarRol(Request $request)
    {
        if ($request->ajax()) {
            $rol_id = $request->input('id');
            $nombre = $request->input('nombre');
            $usuario = Auth::user();

            if ($rol_id == '0') {
                $rol = new Rol();
                $rol->usuario_creador_id = $usuario->id;

            } else {

                $rol = Rol::find($rol_id);
                $rol->usuario_modificador_id = $usuario->id;
            }
            $rol->nombre = $nombre;
            $rol->save();
            $data = Respuesta::success(null, "Datos Obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function eliminarRol(Request $request)
    {

        if ($request->ajax()) {
            $rol_id = $request->input('rol');
            $usuario = Auth::user();
            $rol = Rol::find($rol_id);
            $rol->usuario_eliminador_id = $usuario->id;
            $rol->save();
            Rol::destroy($rol_id);

            $data = Respuesta::success(null, "Se elimino con exito");

        } else {

            $data = Respuesta::error(null, "Error al obtener los datos");
        }

        return $data;

    }
}
