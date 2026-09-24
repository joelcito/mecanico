<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\MovimientoCaja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MovimientoCajaController extends Controller
{
    public function listado()
    {
        return view('movimientosCaja.listado');
    }


    public function ajaxListado(Request $request)
    {
        try {

            $movimientos = MovimientoCaja::with([
                'caja.usuario',
                'caja.sucursal',
            ])
                ->whereNull('pago_id')
                ->orderByDesc('id')
                ->get();

            $listado = view(
                'movimientosCaja.ajaxListado',
                compact('movimientos')
            )->render();

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
    $cajas = Caja::where('estado', 'ABIERTA')
        ->with([
            'usuario',
            'sucursal'
        ])
        ->orderByDesc('id')
        ->get();

    return response()->json([
        'estado' => true,
        'cajas' => $cajas
    ]);
}


    public function guardar(Request $request)
    {
        $request->validate([
            'caja_id' => [
                'required',
                'exists:cajas,id'
            ],

            'tipo' => [
                'required',
                'in:INGRESO,EGRESO'
            ],

            'metodo_pago' => [
                'required',
                'in:EFECTIVO,QR,TRANSFERENCIA'
            ],

            'monto' => [
                'required',
                'numeric',
                'min:0.01'
            ],

            'origen_dinero' => [
                'nullable',
                'string',
                'max:50'
            ],

            'descripcion' => [
                'nullable',
                'string'
            ],
        ], [
            'caja_id.required' => 'Debe seleccionar una caja.',
            'caja_id.exists' => 'La caja seleccionada no existe.',
            'tipo.required' => 'Debe seleccionar el tipo de movimiento.',
            'tipo.in' => 'El tipo de movimiento no es válido.',
            'metodo_pago.required' => 'Debe seleccionar el método de pago.',
            'metodo_pago.in' => 'El método de pago no es válido.',
            'monto.required' => 'Debe ingresar el monto.',
            'monto.numeric' => 'El monto debe ser numérico.',
            'monto.min' => 'El monto debe ser mayor a 0.',
            'origen_dinero.max' => 'El origen del dinero no puede superar los 50 caracteres.',
        ]);


        $caja = Caja::findOrFail($request->caja_id);

        if ($caja->estado !== 'ABIERTA') {

            return response()->json([
                'estado' => false,
                'message' => 'No se puede registrar un movimiento en una caja cerrada.'
            ], 422);
        }


        DB::beginTransaction();

        try {

            $usuarioId = Auth::id();
            $movimiento = MovimientoCaja::create([
                'caja_id' => $caja->id,
                'pago_id' => null,
                'tipo' => $request->tipo,
                'metodo_pago' => $request->metodo_pago,
                'monto' => $request->monto,
                'origen_dinero' => $request->origen_dinero ?? 'MANUAL',
                'descripcion' => $request->descripcion,
                'fecha' => now(),
                'estado' => 'ACTIVO',
                'usuario_creador_id' => $usuarioId,

            ]);

            if ($request->tipo === 'INGRESO') {

                $caja->increment(
                    'total_ingresos',
                    $request->monto
                );

            } else {

                $caja->increment(
                    'total_egresos',
                    $request->monto
                );

            }
            DB::commit();
            return response()->json([
                'estado' => true,
                'message' => 'Movimiento registrado correctamente.',
                'data' => [
                    'movimiento' => $movimiento
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


    public function anular($id)
    {
        $movimiento = MovimientoCaja::with('caja')
            ->findOrFail($id);

        if ($movimiento->estado !== 'ACTIVO') {
            return response()->json([
                'estado' => false,
                'message' => 'El movimiento ya se encuentra anulado.'
            ], 422);
        }

        if (!$movimiento->caja) {
            return response()->json([
                'estado' => false,
                'message' => 'La caja asociada al movimiento no existe.'
            ], 422);
        }

        $caja = $movimiento->caja;

        if ($caja->estado !== 'ABIERTA') {
            return response()->json([
                'estado' => false,
                'message' => 'No se puede anular un movimiento de una caja cerrada.'
            ], 422);
        }

        DB::beginTransaction();

        try {

            $usuarioId = Auth::id();
            $movimiento->update([
                'estado' => 'ANULADO',
                'usuario_modificador_id' => $usuarioId,
            ]);
            if ($movimiento->tipo === 'INGRESO') {
                $caja->decrement(
                    'total_ingresos',
                    $movimiento->monto
                );

            } elseif ($movimiento->tipo === 'EGRESO') {
                $caja->decrement(
                    'total_egresos',
                    $movimiento->monto
                );
            }
            DB::commit();
            return response()->json([
                'estado' => true,
                'message' => 'El movimiento fue anulado correctamente.'
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
