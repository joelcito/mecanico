<?php

namespace App\Http\Controllers;

use App\Models\OrdenServicio;
use App\Models\Reparacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReparacionController extends Controller
{
    public function datos($id)
    {
        try {

            $orden = OrdenServicio::with([
                'reparacion.tecnico',
            ])->findOrFail($id);

            $tecnicos = User::where('estado', 'ACTIVO')
                ->orderBy('nombres')
                ->get();

            return response()->json([
                'estado' => true,
                'data' => [
                    'reparacion' => $orden->reparacion,
                    'tecnicos' => $tecnicos,
                ]
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'estado' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function guardar(Request $request, $id)
    {
        try {

            $orden = OrdenServicio::findOrFail($id);

            if ($orden->estado !== 'EN_REPARACION') {
                return response()->json([
                    'estado' => false,
                    'message' => 'La orden no se encuentra en reparación.'
                ], 422);
            }

            $request->validate([
                'tecnico_id' => 'nullable|exists:users,id',
                'trabajos_realizados' => 'nullable|string',
                'observaciones' => 'nullable|string',
            ]);

            $reparacion = Reparacion::firstOrNew([
                'orden_servicio_id' => $orden->id
            ]);

            if (!$reparacion->exists) {
                $reparacion->fecha_inicio = now();
                $reparacion->estado = 'EN_PROCESO';
                $reparacion->usuario_creador_id = Auth::id();
            } else {
                $reparacion->usuario_modificador_id = Auth::id();
            }

            $reparacion->tecnico_id = $request->tecnico_id;
            $reparacion->trabajos_realizados = $request->trabajos_realizados;
            $reparacion->observaciones = $request->observaciones;

            $reparacion->save();

            return response()->json([
                'estado' => true,
                'message' => 'La información de reparación fue guardada correctamente.',
                'data' => $reparacion->load('tecnico')
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'estado' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function finalizar($id)
    {
        try {

            $orden = OrdenServicio::findOrFail($id);

            if ($orden->estado !== 'EN_REPARACION') {
                return response()->json([
                    'estado' => false,
                    'message' => 'La orden no se encuentra en reparación.'
                ], 422);
            }

            $reparacion = Reparacion::where(
                'orden_servicio_id',
                $orden->id
            )->first();

            if (!$reparacion) {
                return response()->json([
                    'estado' => false,
                    'message' => 'Debe registrar la información de la reparación antes de finalizarla.'
                ], 422);
            }

            if (!$reparacion->trabajos_realizados) {
                return response()->json([
                    'estado' => false,
                    'message' => 'Debe registrar los trabajos realizados antes de finalizar la reparación.'
                ], 422);
            }

            $reparacion->update([
                'fecha_fin' => now(),
                'estado' => 'FINALIZADA',
                'usuario_modificador_id' => Auth::id(),
            ]);

            $orden->update([
                'estado' => 'LISTO',
                'usuario_modificador_id' => Auth::id(),
            ]);

            return response()->json([
                'estado' => true,
                'message' => 'La reparación fue finalizada correctamente.',
                'data' => [
                    'reparacion' => $reparacion,
                    'orden' => $orden
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