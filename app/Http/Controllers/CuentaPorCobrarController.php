<?php

namespace App\Http\Controllers;

use App\Models\OrdenServicio;
use Illuminate\Http\Request;

class CuentaPorCobrarController extends Controller
{
    public function listado()
    {
        return view('cuentasPorCobrar.listado');
    }

    public function ajaxListado(Request $request)
    {
        try {

            $ordenes = OrdenServicio::with([
                'vehiculo.cliente.user',
                'vehiculo.marca',
                'cotizacionActual',
            ])
                ->whereHas('cotizaciones', function ($query) {
                    $query->where('estado', 'APROBADA');
                })
                ->orderByDesc('id')
                ->get();

            $cuentas = $ordenes->map(function ($orden) {

                $cotizacion = $orden->cotizacionActual;

                if (!$cotizacion || $cotizacion->estado !== 'APROBADA') {
                    return null;
                }

                $total = (float) $cotizacion->total;

                $pagado = (float) $orden->pagos()
                    ->where('estado', 'ACTIVO')
                    ->sum('monto');

                $saldo = max(0, $total - $pagado);

                // Solo cuentas que todavía tienen saldo
                if ($saldo <= 0) {
                    return null;
                }

                if ($pagado <= 0) {
                    $estadoPago = 'SIN_PAGO';
                } else {
                    $estadoPago = 'PARCIAL';
                }

                return [
                    'id' => $orden->id,
                    'numero_orden' => $orden->numero_orden,

                    'cliente' => $orden->vehiculo?->cliente?->user
                        ? trim(
                            $orden->vehiculo->cliente->user->nombres . ' ' .
                            $orden->vehiculo->cliente->user->ap_paterno . ' ' .
                            $orden->vehiculo->cliente->user->ap_materno
                        )
                        : 'Sin cliente',

                    'vehiculo' => $orden->vehiculo
                        ? trim(
                            ($orden->vehiculo->marca?->nombre ?? '') . ' ' .
                            ($orden->vehiculo->modelo ?? '')
                        )
                        : 'Sin vehículo',

                    'placa' => $orden->vehiculo?->placa ?? '',

                    'total' => $total,
                    'pagado' => $pagado,
                    'saldo' => $saldo,
                    'estado_pago' => $estadoPago,
                ];
            })
            ->filter()
            ->values();

            $listado = view(
                'cuentasPorCobrar.ajaxListado',
                compact('cuentas')
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
}