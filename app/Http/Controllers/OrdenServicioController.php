<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\OrdenServicio;
use App\Models\Vehiculo;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrdenServicioController extends Controller
{
    
    public function listado()
    {
        return view('ordenServicio.listado');
    }

    
    public function ajaxListado(Request $request)
{
    if (!$request->ajax()) {

        return response()->json(
            Respuesta::error(
                null,
                'Solicitud no válida'
            )->toArray(),
            400
        );
    }

    $ordenes = OrdenServicio::with([
        'vehiculo.cliente.user',
        'vehiculo.marca',
    ])
        ->orderByDesc('id')
        ->get();

    $listado = view(
        'ordenServicio.ajaxListado',
        compact('ordenes')
    )->render();

    return response()->json(
        Respuesta::success(
            [
                'listado' => $listado
            ],
            'Listado cargado correctamente'
        )->toArray()
    );
}

    
    public function nuevo()
    {
        $vehiculos = Vehiculo::with([
            'cliente.user',
            'marca'
        ])
            ->where('estado', 'ACTIVO')
            ->orderBy('id', 'desc')
            ->get();

        return view(
            'ordenServicio.nuevo',
            compact('vehiculos')
        );
    }

    /**
     * Guardar recepción
     */
    public function guardar(Request $request)
    {
        $request->validate([
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'fecha_recepcion' => 'required|date',
            'kilometraje' => 'nullable|integer|min:0',
            'motivo_ingreso' => 'nullable|string|max:255',
            'nivel_combustible' => 'nullable|numeric|min:0|max:100',
            'observaciones' => 'nullable|string',
        ]);

        try {

            DB::beginTransaction();

            /*
             * Generar número de orden
             */
            $ultimaOrden = OrdenServicio::withTrashed()
                ->orderByDesc('id')
                ->first();

            $siguienteNumero = $ultimaOrden
                ? $ultimaOrden->id + 1
                : 1;

            $numeroOrden = 'OR-' . str_pad(
                $siguienteNumero,
                6,
                '0',
                STR_PAD_LEFT
            );

            $orden = OrdenServicio::create([
                'numero_orden' => $numeroOrden,
                'vehiculo_id' => $request->vehiculo_id,
                'fecha_recepcion' => $request->fecha_recepcion,
                'kilometraje' => $request->kilometraje,
                'motivo_ingreso' => $request->motivo_ingreso,
                'nivel_combustible' => $request->nivel_combustible,
                'observaciones' => $request->observaciones,
                'estado' => 'RECIBIDO',
                'usuario_creador_id' => Auth::id(),
            ]);

            DB::commit();

            return response()->json(
                Respuesta::success(
                    $orden,
                    'Orden de servicio registrada correctamente'
                )->toArray()
            );

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json(
                Respuesta::error(
                    null,
                    'No se pudo registrar la orden: ' . $e->getMessage()
                )->toArray(),
                500
            );
        }
    }


    public function detalle($id)
{
    $orden = OrdenServicio::with([
        'vehiculo.cliente.user',
        'vehiculo.marca',
    ])->findOrFail($id);

    return view(
        'ordenServicio.detalle',
        compact('orden')
    );
}
}