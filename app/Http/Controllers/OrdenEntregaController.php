<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use App\Models\OrdenServicio;
use App\Models\Reparacion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrdenEntregaController extends Controller
{
    public function verificarDeuda($id)
    {
        $orden = OrdenServicio::with(['cotizacionActual', 'reparacion'])->findOrFail($id);
        $resumen = $this->resumenPago($orden);
        $motivo = null;

        if ($orden->estado !== 'LISTO') {
            $motivo = 'La orden debe estar en estado LISTO para verificar la entrega.';
        } elseif ($orden->reparacion?->estado !== 'FINALIZADA') {
            $motivo = 'La reparación de esta orden todavía no ha finalizado.';
        } elseif (!$orden->cotizacionActual || $orden->cotizacionActual->estado !== 'APROBADA') {
            $motivo = 'La orden no tiene una cotización aprobada para verificar el pago.';
        }

        $resumen['puede_entregar'] = $motivo === null && $resumen['saldo'] <= 0;
        $resumen['tiene_deuda'] = $resumen['saldo'] > 0;
        $resumen['motivo'] = $motivo;

        return response()->json([
            'estado' => true,
            'data' => $resumen,
        ]);
    }

    public function entregar(Request $request, $id)
    {
        $datos = $request->validate([
            'observaciones' => ['nullable', 'string', 'max:5000'],
        ]);

        $entrega = DB::transaction(function () use ($id, $datos) {
            $orden = OrdenServicio::lockForUpdate()->findOrFail($id);
            if ($orden->estado !== 'LISTO') {
                throw ValidationException::withMessages([
                    'orden' => 'Solo se puede entregar una orden en estado LISTO.',
                ]);
            }

            $reparacion = Reparacion::where('orden_servicio_id', $orden->id)
                ->lockForUpdate()
                ->first();
            if (!$reparacion || $reparacion->estado !== 'FINALIZADA') {
                throw ValidationException::withMessages([
                    'reparacion' => 'La reparación de esta orden todavía no ha finalizado.',
                ]);
            }

            $orden->load('cotizacionActual');
            if (!$orden->cotizacionActual || $orden->cotizacionActual->estado !== 'APROBADA') {
                throw ValidationException::withMessages([
                    'cotizacion' => 'La orden no tiene una cotización aprobada.',
                ]);
            }

            $resumen = $this->resumenPago($orden);
            if ($resumen['saldo'] > 0) {
                throw ValidationException::withMessages([
                    'saldo' => 'No se puede realizar la entrega mientras exista un saldo pendiente.',
                ]);
            }

            if (Entrega::where('orden_servicio_id', $orden->id)->exists()) {
                throw ValidationException::withMessages([
                    'entrega' => 'La entrega de esta orden ya fue registrada.',
                ]);
            }

            $entrega = Entrega::create([
                'orden_servicio_id' => $orden->id,
                'usuario_id' => Auth::id(),
                'fecha_entrega' => now(),
                'observaciones' => $datos['observaciones'] ?? null,
            ]);

            $orden->update([
                'estado' => 'ENTREGADO',
                'usuario_modificador_id' => Auth::id(),
            ]);

            return $entrega->load('usuario');
        });

        return response()->json([
            'estado' => true,
            'message' => 'La entrega fue registrada correctamente.',
            'data' => [
                'entrega' => $entrega,
                'boleta_url' => route('ordenServicio.entrega.boleta', $id),
            ],
        ]);
    }

    public function boleta($id)
    {
        $orden = OrdenServicio::with([
            'vehiculo.cliente.user',
            'vehiculo.marca',
            'cotizacionActual',
            'reparacion.detalles.producto',
            'entrega.usuario',
        ])->findOrFail($id);

        abort_unless(
            $orden->estado === 'ENTREGADO' && $orden->entrega,
            404,
            'La boleta estará disponible después de registrar la entrega.'
        );

        $resumenPago = $this->resumenPago($orden);
        $pdf = Pdf::loadView('ordenServicio.boletaEntrega', compact('orden', 'resumenPago'))
            ->setPaper('a4');

        return $pdf->stream('boleta-entrega-ENT-' . str_pad((string) $orden->entrega->id, 6, '0', STR_PAD_LEFT) . '.pdf');
    }

    private function resumenPago(OrdenServicio $orden): array
{
    $orden->loadMissing('cotizacionActual');

    $total = round((float) ($orden->cotizacionActual?->total ?? 0), 2);
    $pagado = round((float) $orden->total_pagado, 2);
    $saldo = round(max(0, $total - $pagado), 2);

    return [
        'total' => $total,
        'pagado' => $pagado,
        'saldo' => $saldo,
        'estado_pago' => $orden->estado_pago,
    ];
}
}