<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Marca;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Utils\Respuesta;

class VehiculoController extends Controller
{
    public function listado()
{
    $marcas = Marca::whereNull('deleted_at')
        ->where('estado', 'ACTIVO')
        ->where('tipo', 'AUTO')
        ->orderBy('nombre', 'asc')
        ->get();

    $clientes = Cliente::with('user')
        ->whereNull('deleted_at')
        ->where('estado', 'ACTIVO')
        ->orderBy('id', 'desc')
        ->get();

    return view(
        'vehiculo.listado',
        compact('marcas', 'clientes')
    );
}

    public function ajaxListado(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json(
                Respuesta::error(
                    null,
                    'Error al obtener los datos'
                )->toArray()
            );
        }

        $vehiculos = Vehiculo::with([
                'cliente.user',
                'marca'
            ])
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->get();

        $listado = view(
            'vehiculo.ajaxListado',
            compact('vehiculos')
        )->render();

        return response()->json(
            Respuesta::success(
                [
                    'listado' => $listado
                ],
                'Vehículos obtenidos correctamente.'
            )->toArray()
        );
    }

    public function guardarVehiculo(Request $request)
    {
        $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'marca_id' => ['required', 'exists:marcas,id'],
            'modelo' => ['required', 'string', 'max:100'],
            'anio' => ['nullable', 'integer', 'min:1900'],
            'placa' => ['required', 'string', 'max:20'],
            'color' => ['nullable', 'string', 'max:50'],
            'tipo_vehiculo' => [
                'required',
                Rule::in(['AUTOMOVIL', 'MOTOCICLETA'])
            ],

            'tipo_propulsion' => 'required|in:COMBUSTION,ELECTRICO,HIBRIDO',
            'vin' => [ 'nullable', 'string', 'max:50'],
            'numero_motor' => ['nullable', 'string', 'max:50'],
            'observaciones' => ['nullable', 'string'],
            'estado' => ['nullable', 'string', 'max:50'],
        ], [
            'required' => 'El campo :attribute es obligatorio.',
            'exists' => 'El :attribute seleccionado no es válido.',
            'string' => 'El campo :attribute debe ser texto.',
            'max' => 'El campo :attribute no debe superar los :max caracteres.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'min' => 'El campo :attribute debe ser mayor o igual a :min.',
            'in' => 'El valor seleccionado para :attribute no es válido.',
        ], [
            'cliente_id' => 'cliente',
            'marca_id' => 'marca',
            'modelo' => 'modelo',
            'anio' => 'año',
            'placa' => 'placa',
            'color' => 'color',
            'tipo_vehiculo' => 'tipo de vehículo',
            'vin' => 'VIN',
            'numero_motor' => 'número de motor',
            'observaciones' => 'observaciones',
            'estado' => 'estado',
        ]);

        // Normalizar datos
        $placa = strtoupper(trim($request->placa));

        $vin = $request->vin
            ? strtoupper(trim($request->vin))
            : null;

        if ($request->id) {
            $vehiculo = Vehiculo::whereNull('deleted_at')->find($request->id);

            if (!$vehiculo) {
                return response()->json(
                    Respuesta::error(
                        null,
                        'El vehículo no existe.'
                    )->toArray(),
                    404
                );
            }

            $placaExiste = Vehiculo::where('placa', $placa)
                ->where('id', '!=', $vehiculo->id)
                ->whereNull('deleted_at')
                ->exists();

            if ($placaExiste) {
                return response()->json(
                    Respuesta::error(
                        null,
                        'La placa ya se encuentra registrada.'
                    )->toArray(),
                    422
                );
            }

            if ($vin) {
                $vinExiste = Vehiculo::where('vin', $vin)
                    ->where('id', '!=', $vehiculo->id)
                    ->whereNull('deleted_at')
                    ->exists();
                if ($vinExiste) {
                    return response()->json(
                        Respuesta::error(
                            null,
                            'El VIN ya se encuentra registrado.'
                        )->toArray(),
                        422
                    );
                }
            }

            $vehiculo->cliente_id = $request->cliente_id;
            $vehiculo->marca_id = $request->marca_id;
            $vehiculo->modelo = $request->modelo;
            $vehiculo->anio = $request->anio;
            $vehiculo->placa = $placa;
            $vehiculo->color = $request->color;
            $vehiculo->tipo_vehiculo = $request->tipo_vehiculo;
          
            $vehiculo->vin = $vin;
            $vehiculo->numero_motor = $request->numero_motor;
            $vehiculo->tipo_propulsion = $request->tipo_propulsion;
            $vehiculo->observaciones = $request->observaciones;

            if ($request->has('estado')) {
                $vehiculo->estado = $request->estado;
            }

            $vehiculo->usuario_modificador_id = Auth::id();
            $vehiculo->save();
            return response()->json(
                Respuesta::success(
                    $vehiculo,
                    'Vehículo actualizado correctamente.'
                )->toArray()
            );
        }

        $placaExiste = Vehiculo::where('placa', $placa)
            ->whereNull('deleted_at')
            ->exists();

        if ($placaExiste) {
            return response()->json(
                Respuesta::error(
                    null,
                    'La placa ya se encuentra registrada.'
                )->toArray(),
                422
            );
        }

        if ($vin) {
            $vinExiste = Vehiculo::where('vin', $vin)
                ->whereNull('deleted_at')
                ->exists();

            if ($vinExiste) {
                return response()->json(
                    Respuesta::error(
                        null,
                        'El VIN ya se encuentra registrado.'
                    )->toArray(),
                    422
                );
            }
        }


        $vehiculo = new Vehiculo();

        $vehiculo->cliente_id = $request->cliente_id;
        $vehiculo->marca_id = $request->marca_id;
        $vehiculo->modelo = $request->modelo;
        $vehiculo->anio = $request->anio;
        $vehiculo->placa = $placa;
        $vehiculo->color = $request->color;
        $vehiculo->tipo_vehiculo = $request->tipo_vehiculo;
        $vehiculo->vin = $vin;
        $vehiculo->numero_motor = $request->numero_motor;
        $vehiculo->observaciones = $request->observaciones;
        $vehiculo->estado = 'ACTIVO';
        $vehiculo->usuario_creador_id = Auth::id();

        $vehiculo->save();

        return response()->json(
            Respuesta::success(
                $vehiculo,
                'Vehículo registrado correctamente.'
            )->toArray()
        );
    }

    public function eliminarVehiculo(Request $request)
    {
        $vehiculo = Vehiculo::whereNull('deleted_at')
            ->find($request->id);

        if (!$vehiculo) {
            return response()->json(
                Respuesta::error(
                    null,
                    'El vehículo no existe.'
                )->toArray(),
                404
            );
        }

        $vehiculo->usuario_eliminador_id = Auth::id();
        $vehiculo->deleted_at = now();
        $vehiculo->estado = 'INACTIVO';

        $vehiculo->save();

        return response()->json(
            Respuesta::success(
                null,
                'Vehículo eliminado correctamente.'
            )->toArray()
        );
    }
}