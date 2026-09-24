<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CajaController extends Controller
{
    public function listado()
    {
        return view('cajas.listado');
    }

    public function ajaxListado(Request $request)
    {
        try {

            $cajas = Caja::with([
                'usuario',
                'sucursal'
            ])
            ->orderByDesc('id')
            ->get();

            $listado = view('cajas.ajaxListado', compact('cajas'))->render();

            return response()->json([
                'estado' => true,
                'data' => [
                    'listado' => $listado
                ]
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'estado' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function crear()
    {
        return view('cajas.abrir');
    }

    public function abrir(Request $request)
    {
        $request->validate([
            'sucursal_id' => [
                'required',
                'exists:sucursales,id',
            ],
            'monto_apertura' => [
                'required',
                'numeric',
                'min:0',
            ],
        ], [
            'sucursal_id.required' => 'Debe seleccionar una sucursal.',
            'sucursal_id.exists' => 'La sucursal seleccionada no existe.',
            'monto_apertura.required' => 'Debe ingresar el monto de apertura.',
            'monto_apertura.numeric' => 'El monto de apertura debe ser numérico.',
            'monto_apertura.min' => 'El monto de apertura no puede ser negativo.',
        ]);

        $usuarioId = Auth::id();

        $cajaAbierta = Caja::where('usuario_id', $usuarioId)
            ->where('sucursal_id', $request->sucursal_id)
            ->where('estado', 'ABIERTA')
            ->first();

        if ($cajaAbierta) {
            return response()->json([
                'estado' => false,
                'message' => 'Ya tienes una caja abierta en esta sucursal.'
            ], 422);
        }

        DB::beginTransaction();

        try {

            Caja::create([
                'usuario_id' => $usuarioId,
                'sucursal_id' => $request->sucursal_id,

                'monto_apertura' => $request->monto_apertura,
                'total_ingresos' => 0,
                'total_egresos' => 0,

                'fecha_apertura' => now(),
                'estado' => 'ABIERTA',

                'usuario_creador_id' => $usuarioId,
            ]);

            DB::commit();

            return response()->json([
                'estado' => true,
                'message' => 'La caja fue abierta correctamente.'
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'estado' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function actual($id)
    {
        $caja = Caja::with([
            'usuario',
            'sucursal',
            'pagos.ordenServicio',
            'movimientos'
        ])->findOrFail($id);

        return view('cajas.actual', compact('caja'));
    }

    public function cerrar($id)
    {
        $caja = Caja::findOrFail($id);
        if ($caja->estado !== 'ABIERTA') {
            return response()->json([
                'estado' => false,
                'message' => 'La caja seleccionada no está abierta.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $caja->update([
                'estado' => 'CERRADA',
                'fecha_cierre' => now(),
                'monto_cierre' => $caja->monto_apertura
                    + $caja->total_ingresos
                    - $caja->total_egresos,
                'usuario_modificador_id' => Auth::id(),
            ]);
            DB::commit();
            return response()->json([
                'estado' => true,
                'message' => 'La caja fue cerrada correctamente.'
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