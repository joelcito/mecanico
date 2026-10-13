<?php

namespace App\Http\Controllers;

use App\Models\OrdenServicio;
use App\Models\OrdenDiagnostico;
use App\Models\OrdenDiagnosticoDetalle;
use App\Models\OrdenFoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class OrdenDiagnosticoController extends Controller
{
    public function crear($id)
    {
        $orden = OrdenServicio::with([
            'vehiculo.cliente.user',
            'vehiculo.marca',
            'inspeccionActual.detalles',
            'inspeccionActual.fotos',
            'diagnosticoActual.detalles.fotos',
        ])->findOrFail($id);

        if ($orden->estado !== 'EN_DIAGNOSTICO') {
            return redirect()
                ->route('ordenServicio.detalle', $orden->id)
                ->with(
                    'error',
                    'La orden no se encuentra disponible para diagnóstico.'
                );
        }

        return view('ordenServicio.diagnostico', compact('orden'));
    }

    public function guardar(Request $request, $id)
    {
        $request->validate([
            'descripcion' => 'required|string|max:10000',
            'observaciones' => 'nullable|string|max:10000',

            'detalles' => 'required|array|min:1',
            'detalles.*.descripcion' => 'required|string|max:1000',
            'detalles.*.tipo' => 'required|in:HALLAZGO,RECOMENDACION',
            'detalles.*.prioridad' => 'required|in:BAJA,MEDIA,ALTA,CRITICA',
            'detalles.*.observacion' => 'nullable|string|max:1000',

            'detalles.*.fotos' => 'nullable|array|max:10',
            'detalles.*.fotos.*' => 'nullable|array',
            'detalles.*.fotos.*.archivo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
            'detalles.*.fotos.*.descripcion' => 'nullable|string|max:255',
        ], [
            'detalles.*.fotos.*.archivo.image' =>
                'Cada fotografía debe ser una imagen válida.',
            'detalles.*.fotos.*.archivo.max' =>
                'Cada fotografía debe pesar como máximo 5 MB.',
            'detalles.*.fotos.*.archivo.mimes' =>
                'Las fotografías deben ser JPG, JPEG, PNG o WEBP.',
        ]);

        $archivosGuardados = [];

        try {
            DB::beginTransaction();

            $orden = OrdenServicio::whereKey($id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($orden->estado !== 'EN_DIAGNOSTICO') {
                throw new \RuntimeException(
                    'La orden no se encuentra disponible para diagnóstico.'
                );
            }

            $diagnostico = OrdenDiagnostico::create([
                'orden_servicio_id' => $orden->id,
                'usuario_diagnostico_id' => Auth::id(),
                'fecha' => now(),
                'descripcion' => $request->descripcion,
                'observaciones' => $request->observaciones,
                'estado' => 'FINALIZADO',
                'usuario_creador_id' => Auth::id(),
            ]);

            foreach ($request->input('detalles', []) as $indice => $detalle) {
                $detalleDiagnostico = OrdenDiagnosticoDetalle::create([
                    'orden_diagnostico_id' => $diagnostico->id,
                    'descripcion' => $detalle['descripcion'],
                    'tipo' => $detalle['tipo'],
                    'prioridad' => $detalle['prioridad'],
                    'observacion' => $detalle['observacion'] ?? null,
                    'estado' => 'ACTIVO',
                    'usuario_creador_id' => Auth::id(),
                ]);

                $fotos = $request->file("detalles.$indice.fotos", []);

                foreach ($fotos as $foto) {
                    if (
                        !is_array($foto) ||
                        empty($foto['archivo']) ||
                        !$foto['archivo']->isValid()
                    ) {
                        continue;
                    }

                    $ruta = $foto['archivo']->store(
                        'ordenes/' . $orden->id . '/diagnostico',
                        'public'
                    );

                    if (!$ruta) {
                        throw new \RuntimeException(
                            'No se pudo guardar una fotografía del diagnóstico.'
                        );
                    }

                    $archivosGuardados[] = $ruta;

                    OrdenFoto::create([
                        'orden_servicio_id' => $orden->id,
                        'orden_inspeccion_id' => null,
                        'orden_diagnostico_detalle_id' => $detalleDiagnostico->id,
                        'tipo' => 'DIAGNOSTICO',
                        'ruta' => $ruta,
                        'descripcion' => $foto['descripcion'] ?? null,
                        'estado' => 'ACTIVO',
                        'usuario_creador_id' => Auth::id(),
                    ]);
                }
            }

            $orden->update([
                'estado' => 'EN_COTIZACION',
                'usuario_modificador_id' => Auth::id(),
            ]);

            DB::commit();

            return redirect()
                ->route('ordenServicio.detalle', $orden->id)
                ->with('success', 'Diagnóstico registrado correctamente.');

        } catch (\Throwable $e) {
            DB::rollBack();

            if (!empty($archivosGuardados)) {
                Storage::disk('public')->delete($archivosGuardados);
            }

            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'No se pudo registrar el diagnóstico. Verifica los datos y las fotografías e inténtalo nuevamente.'
                );
        }
    }
}
