<?php

namespace App\Http\Controllers;

use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MovimientoInventarioController extends Controller
{
    
    public function guardarIngreso(Request $request)
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

        DB::beginTransaction();

        try {

            $producto = Producto::whereNull('deleted_at')
                ->lockForUpdate()
                ->findOrFail($request->producto_id);

            if ($producto->estado !== 'ACTIVO') {
                return response()->json([
                    'estado' => false,
                    'message' => 'El producto se encuentra inactivo.'
                ], 422);
            }

            $stockAnterior = (float) $producto->cantidad;
            $cantidad = (float) $request->cantidad;
            $stockNuevo = $stockAnterior + $cantidad;

            $movimiento = MovimientoInventario::create([
                'producto_id' => $producto->id,
                'tipo' => 'ENTRADA',
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'motivo' => $request->motivo,
                'observaciones' => $request->observaciones,
                'fecha' => now(),
                'estado' => 'ACTIVO',
                'usuario_creador_id' => Auth::id(),
            ]);

            $producto->update([
                'cantidad' => $stockNuevo,
                'usuario_modificador_id' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'estado' => true,
                'message' => 'Ingreso registrado correctamente.',
                'data' => [
                    'movimiento' => $movimiento,
                    'producto' => $producto,
                ]
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'estado' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function guardarSalida(Request $request)
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
            'in:REPARACION,PERDIDA,DANIO,AJUSTE_INVENTARIO,OTRO'
        ],
        'observaciones' => [
            'nullable',
            'string'
        ],
    ]);

    DB::beginTransaction();

    try {
        $producto = Producto::whereNull('deleted_at')
            ->lockForUpdate()
            ->findOrFail($request->producto_id);

        if ($producto->estado !== 'ACTIVO') {
            return response()->json([
                'estado' => false,
                'message' => 'El producto se encuentra inactivo.'
            ], 422);
        }

        $stockAnterior = (float) $producto->cantidad;
        $cantidad = (float) $request->cantidad;

        if ($cantidad > $stockAnterior) {
            return response()->json([
                'estado' => false,
                'message' => 'La cantidad a retirar no puede ser mayor al stock disponible.'
            ], 422);
        }

        $stockNuevo = $stockAnterior - $cantidad;

        $movimiento = MovimientoInventario::create([
            'producto_id' => $producto->id,
            'tipo' => 'SALIDA',
            'cantidad' => $cantidad,
            'stock_anterior' => $stockAnterior,
            'stock_nuevo' => $stockNuevo,
            'motivo' => $request->motivo,
            'observaciones' => $request->observaciones,
            'fecha' => now(),
            'estado' => 'ACTIVO',
            'usuario_creador_id' => Auth::id(),
        ]);

        $producto->update([
            'cantidad' => $stockNuevo,
            'usuario_modificador_id' => Auth::id(),
        ]);

        DB::commit();

        return response()->json([
            'estado' => true,
            'message' => 'Salida registrada correctamente.',
            'data' => [
                'movimiento' => $movimiento,
                'producto' => $producto,
            ]
        ]);

    } catch (\Throwable $e) {
        DB::rollBack();

        return response()->json([
            'estado' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

}