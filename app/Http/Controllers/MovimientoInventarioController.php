<?php

namespace App\Http\Controllers;

use App\Services\InventarioService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MovimientoInventarioController extends Controller
{
    public function guardarIngreso(Request $request, InventarioService $inventario)
    {
        $request->validate([
            'producto_id' => [
                'required',
                'exists:productos,id'
            ],
            'cantidad' => [
                'required',
                'numeric',
                'gt:0'
            ],
            'motivo' => [
                'required',
                'in:INGRESO,AJUSTE_INVENTARIO'
            ],
            'observaciones' => [
                'nullable',
                'string'
            ],
        ]);

        try {
            $movimiento = $inventario->registrarIngreso(
                (int) $request->producto_id,
                (float) $request->cantidad,
                $request->motivo,
                $request->observaciones
            );

            return response()->json([
                'estado' => true,
                'message' => 'Ingreso registrado correctamente.',
                'data' => $movimiento->load('producto'),
            ]);

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return response()->json([
                'estado' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function guardarSalida(Request $request, InventarioService $inventario)
{
    $request->validate([
        'producto_id' => [
            'required',
            'exists:productos,id'
        ],
        'cantidad' => [
            'required',
            'numeric',
            'gt:0'
        ],
        'motivo' => [
            'required',
            'in:PERDIDA,DANIO,AJUSTE_INVENTARIO,OTRO'
        ],
        'observaciones' => [
            'nullable',
            'string'
        ],
    ]);

    try {
        $movimiento = $inventario->registrarSalidaManual(
            (int) $request->producto_id,
            (float) $request->cantidad,
            $request->motivo,
            $request->observaciones
        );

        return response()->json([
            'estado' => true,
            'message' => 'Salida registrada correctamente.',
            'data' => $movimiento->load('producto'),
        ]);

    } catch (ValidationException $e) {
        throw $e;
    } catch (\Throwable $e) {
        return response()->json([
            'estado' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

}