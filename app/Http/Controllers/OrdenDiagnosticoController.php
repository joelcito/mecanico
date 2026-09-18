<?php

namespace App\Http\Controllers;

use App\Models\OrdenServicio;
use App\Models\OrdenDiagnostico;
use App\Models\OrdenDiagnosticoDetalle;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrdenDiagnosticoController extends Controller
{
    /**
     * Mostrar formulario de diagnóstico.
     */
    public function crear($id)
    {
        $orden = OrdenServicio::with([
            'vehiculo.cliente.user',
            'vehiculo.marca',
            'inspeccionActual.detalles',
            'inspeccionActual.fotos',
            'diagnosticoActual.detalles',
        ])->findOrFail($id);

        if ($orden->estado !== 'EN_DIAGNOSTICO') {
            return redirect()
                ->route('ordenServicio.detalle', $orden->id)
                ->with(
                    'error',
                    'La orden no se encuentra disponible para diagnóstico.'
                );
        }

        return view(
            'ordenServicio.diagnostico',
            compact('orden')
        );
    }

    /**
     * Guardar diagnóstico.
     */
    public function guardar(Request $request, $id)
    {
        $request->validate([
            'descripcion' => 'required|string',
            'observaciones' => 'nullable|string',

            'detalles' => 'required|array|min:1',

            'detalles.*.descripcion' =>
                'required|string|max:1000',

            'detalles.*.tipo' =>
                'required|in:HALLAZGO,RECOMENDACION',

            'detalles.*.prioridad' =>
                'required|in:BAJA,MEDIA,ALTA,CRITICA',

            'detalles.*.observacion' =>
                'nullable|string|max:1000',
        ]);

        try {

            DB::beginTransaction();

            $orden = OrdenServicio::findOrFail($id);

            if ($orden->estado !== 'EN_DIAGNOSTICO') {
                throw new \Exception(
                    'La orden no se encuentra disponible para diagnóstico.'
                );
            }

            /*
             * Crear diagnóstico
             */
            $diagnostico = OrdenDiagnostico::create([
                'orden_servicio_id' => $orden->id,
                'usuario_diagnostico_id' => Auth::id(),
                'fecha' => now(),
                'descripcion' => $request->descripcion,
                'observaciones' => $request->observaciones,
                'estado' => 'FINALIZADO',
                'usuario_creador_id' => Auth::id(),
            ]);

            /*
             * Crear detalles
             */
            foreach ($request->detalles as $detalle) {

                OrdenDiagnosticoDetalle::create([
                    'orden_diagnostico_id' => $diagnostico->id,
                    'descripcion' => $detalle['descripcion'],
                    'tipo' => $detalle['tipo'],
                    'prioridad' => $detalle['prioridad'],
                    'observacion' => $detalle['observacion'] ?? null,
                    'estado' => 'ACTIVO',
                    'usuario_creador_id' => Auth::id(),
                ]);
            }

            /*
             * Pasar la orden a cotización
             */
            $orden->update([
                'estado' => 'EN_COTIZACION',
                'usuario_modificador_id' => Auth::id(),
            ]);

            DB::commit();

            return redirect()
                ->route(
                    'ordenServicio.detalle',
                    $orden->id
                )
                ->with(
                    'success',
                    'Diagnóstico registrado correctamente.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with(
                    'error',
                    'No se pudo registrar el diagnóstico: ' .
                    $e->getMessage()
                );
        }
    }
}