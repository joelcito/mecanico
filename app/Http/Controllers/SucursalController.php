<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SucursalController extends Controller
{
    public function listado()
    {
        return view('sucursal.listado');
    }

    public function ajaxListado(Request $request)
    {
        if ($request->ajax()) {
            $sucursales = Sucursal::all();
            $valores = [
                'listado' => view('sucursal.ajaxListado')->with(compact('sucursales'))->render()
            ];

            $data = Respuesta::success($valores, "Datos Obtenidos correctamente");

        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }

    public function guardarSucursal(Request $request)
    {
        if ($request->ajax()) {
            $sucursal_id = $request->input('id');
            $nombre = $request->input('nombre');
            $direccion = $request->input('direccion');
            $telefono = $request->input('telefono');
            $usuario = Auth::user();

            if ($sucursal_id == '0') {
                $sucursal = new Sucursal();
                $sucursal->usuario_creador_id = $usuario->id;

            } else {
                $sucursal = Sucursal::find($sucursal_id);
                $sucursal->usuario_modificador_id = $usuario->id;
            }

            $sucursal->nombre = $nombre;
            $sucursal->direccion = $direccion;
            $sucursal->telefono = $telefono;
            $sucursal->estado = 1;
            $sucursal->save();
            $data = Respuesta::success(null, "Datos Obtenidos correctamente");
        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;

    }

    public function eliminarSucursal(Request $request)
    {
        if ($request->ajax()) {
            $sucursal_id = $request->input('sucursal');
            $usuario = Auth::user();
            $sucursal = Sucursal::find($sucursal_id);
            $sucursal->usuario_eliminador_id = $usuario->id;
            $sucursal->save();
            Sucursal::destroy($sucursal_id);
            $data = Respuesta::success(null, "Se elimino con exito");
        } else {
            $data = Respuesta::error(null, "Error al obtener los datos");
        }
        return $data;
    }
}
