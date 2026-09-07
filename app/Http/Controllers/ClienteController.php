<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\User;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    public function listado()
    {
        return view('cliente.listado');
    }

    public function ajaxListado(Request $request)
    {
        if (!$request->ajax()) {
            return Respuesta::error(null, 'Error al obtener los datos');
        }

        $clientes = Cliente::with('user')
            ->where('estado', 'ACTIVO')
            ->get();

        $valores = [
            'listado' => view('cliente.ajaxListado')
                ->with(compact('clientes'))
                ->render()
        ];

        return Respuesta::success(
            $valores,
            'Datos obtenidos correctamente'
        );
    }

    public function guardarCliente(Request $request)
    {
        if (!$request->ajax()) {
            return Respuesta::error(null, 'Error en la solicitud');
        }

        $request->validate([
            'id' => ['required'],
            'nombres' => ['required', 'string', 'max:255'],
            'ap_paterno' => ['nullable', 'string', 'max:255'],
            'ap_materno' => ['nullable', 'string', 'max:255'],
            'cedula' => ['nullable', 'string', 'max:255'],
            'celular' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'nit' => ['nullable', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
        ]);

        $usuario = Auth::user();

        if (!$usuario) {
            return Respuesta::error(null, 'Usuario no autenticado');
        }

        DB::beginTransaction();

        try {

            $clienteId = $request->input('id');

            /*
             * CREAR
             */
            if ($clienteId == '0') {

                $user = new User();

                $user->name = $request->input('cedula')
                    ?: 'cliente_' . time();

                $user->email = $request->input('email');

                $user->password = Hash::make(
                    $request->input('password', '12345678')
                );

                $user->nombres = $request->input('nombres');
                $user->ap_paterno = $request->input('ap_paterno');
                $user->ap_materno = $request->input('ap_materno');
                $user->cedula = $request->input('cedula');
                $user->celular = $request->input('celular');

                $user->usuario_creador_id = $usuario->id;

                $user->save();

                $cliente = new Cliente();

                $cliente->user_id = $user->id;
                $cliente->nit = $request->input('nit');
                $cliente->direccion = $request->input('direccion');
                $cliente->razon_social = $request->input('razon_social');
                $cliente->estado = 'ACTIVO';
                $cliente->usuario_creador_id = $usuario->id;

                $cliente->save();

            /*
             * EDITAR
             */
            } else {

                $cliente = Cliente::with('user')->find($clienteId);

                if (!$cliente) {
                    DB::rollBack();

                    return Respuesta::error(
                        null,
                        'Cliente no encontrado'
                    );
                }

                $user = $cliente->user;

                if (!$user) {
                    DB::rollBack();

                    return Respuesta::error(
                        null,
                        'El cliente no tiene un usuario asociado'
                    );
                }

                $user->nombres = $request->input('nombres');
                $user->ap_paterno = $request->input('ap_paterno');
                $user->ap_materno = $request->input('ap_materno');
                $user->cedula = $request->input('cedula');
                $user->celular = $request->input('celular');
                $user->email = $request->input('email');

                $user->usuario_modificador_id = $usuario->id;

                $user->save();

                $cliente->nit = $request->input('nit');
                $cliente->direccion = $request->input('direccion');
                $cliente->razon_social = $request->input('razon_social');

                $cliente->usuario_modificador_id = $usuario->id;

                $cliente->save();
            }

            DB::commit();

            return Respuesta::success(
                null,
                'Cliente guardado correctamente'
            );

        } catch (\Throwable $e) {

            DB::rollBack();

            return Respuesta::error(
                null,
                $e->getMessage()
            );
        }
    }

    public function eliminarCliente(Request $request)
    {
        if (!$request->ajax()) {
            return Respuesta::error(null, 'Error en la solicitud');
        }

        $clienteId = $request->input('cliente');

        $usuario = Auth::user();

        if (!$usuario) {
            return Respuesta::error(null, 'Usuario no autenticado');
        }

        $cliente = Cliente::find($clienteId);

        if (!$cliente) {
            return Respuesta::error(null, 'Cliente no encontrado');
        }

        $cliente->usuario_eliminador_id = $usuario->id;
        $cliente->save();

        $cliente->delete();

        return Respuesta::success(
            null,
            'Cliente eliminado correctamente'
        );
    }
}