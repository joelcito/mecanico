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
use App\Models\OrdenCotizacion;
use App\Models\Reparacion;
use App\Models\User;
use Illuminate\Validation\Rule;

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
                'cotizacionActual',
            ])->findOrFail($id);

            $estadosPermitidos = ['EN_DIAGNOSTICO'];

            if (
                $orden->estado === 'EN_COTIZACION'
                && $orden->diagnosticoActual
                && $orden->cotizacionActual?->estado !== 'APROBADA'
            ) {
                $estadosPermitidos[] = 'EN_COTIZACION';
            }

            if (!in_array($orden->estado, $estadosPermitidos)) {
                return redirect()
                    ->route('ordenServicio.detalle', $orden->id)
                    ->with(
                        'error',
                        'El diagnóstico no se puede editar en esta etapa.'
                    );
            }
            $tecnicos = User::where('rol_id', 2)
            ->orderBy('nombres')
            ->orderBy('ap_paterno')
            ->get();

            return view('ordenServicio.diagnostico', compact('orden', 'tecnicos'));
        }


    
public function guardar(Request $request, $id)
{
    
    $request->validate([
        'descripcion' => 'required|string|max:10000',
        'observaciones' => 'nullable|string|max:10000',

        'detalles' => 'required|array|min:1',
        'detalles.*.id' => 'nullable|integer',
        'detalles.*.descripcion' => 'nullable|string|max:1000',
        'detalles.*.observacion' => 'nullable|string|max:1000',
       
        'detalles.*.tipo' => 'required|in:HALLAZGO,RECOMENDACION',
        'detalles.*.prioridad' => 'required|in:BAJA,MEDIA,ALTA,CRITICA',
       

        'tecnico_id' => [
            'required',
            'integer',
            Rule::exists('users', 'id')->where('rol_id', 2),
        ],

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

        $diagnostico = OrdenDiagnostico::where(
            'orden_servicio_id',
            $orden->id
        )->latest('id')->first();

        $esNuevo = !$diagnostico;

        if (!in_array($orden->estado, [
            'EN_DIAGNOSTICO',
            'EN_COTIZACION',
        ])) {
            throw new \RuntimeException(
                'El diagnóstico no se puede modificar en esta etapa.'
            );
        }

        // Si la orden ya está en cotización, no editar
        // un diagnóstico que tenga una cotización aprobada.
        if ($orden->estado === 'EN_COTIZACION') {
            $cotizacion = OrdenCotizacion::where(
                'orden_servicio_id',
                $orden->id
            )->latest('id')->first();

            if ($cotizacion && $cotizacion->estado === 'APROBADA') {
                throw new \RuntimeException(
                    'No se puede modificar el diagnóstico porque la cotización ya fue aprobada.'
                );
            }

            if (!$diagnostico) {
                throw new \RuntimeException(
                    'No existe un diagnóstico registrado para editar.'
                );
            }
        }

        if ($esNuevo) {
            $diagnostico = new OrdenDiagnostico();
            $diagnostico->orden_servicio_id = $orden->id;
            $diagnostico->usuario_diagnostico_id = Auth::id();
            $diagnostico->fecha = now();
            $diagnostico->estado = 'FINALIZADO';
            $diagnostico->tecnico_id = $request->tecnico_id;
            $diagnostico->usuario_creador_id = Auth::id();
        }

        $diagnostico->descripcion = $request->descripcion;
        $diagnostico->observaciones = $request->observaciones;
        $diagnostico->tecnico_id = $request->tecnico_id;
        $diagnostico->usuario_modificador_id = Auth::id();
        $diagnostico->save();

        foreach ($request->input('detalles', []) as $indice => $datos) {
            $detalleId = $datos['id'] ?? null;

            if ($detalleId) {
                // Solo se permite actualizar detalles del diagnóstico actual.
                $detalleDiagnostico = OrdenDiagnosticoDetalle::where(
                    'orden_diagnostico_id',
                    $diagnostico->id
                )->whereKey($detalleId)->first();

                if (!$detalleDiagnostico) {
                    throw new \RuntimeException(
                        'Uno de los detalles enviados no pertenece a este diagnóstico.'
                    );
                }
            } else {
                $detalleDiagnostico = new OrdenDiagnosticoDetalle();
                $detalleDiagnostico->orden_diagnostico_id = $diagnostico->id;
                $detalleDiagnostico->estado = 'ACTIVO';
                $detalleDiagnostico->usuario_creador_id = Auth::id();
            }

            $detalleDiagnostico->descripcion =
                $datos['descripcion'] ?? null;

            $detalleDiagnostico->tipo = $datos['tipo'];
            $detalleDiagnostico->prioridad = $datos['prioridad'];

            $detalleDiagnostico->observacion =
                $datos['observacion'] ?? null;
            $detalleDiagnostico->usuario_modificador_id = Auth::id();
            $detalleDiagnostico->save();

           
                // Agregar fotografías nuevas sin eliminar las anteriores.
            $fotos = $request->file("detalles.$indice.fotos", []);

            if (!is_array($fotos)) {
                $fotos = [];
            }

            foreach ($fotos as $foto) {
                if (!is_array($foto)) {
                    continue;
                }

                $archivo = $foto['archivo'] ?? null;

                if (!$archivo instanceof \Illuminate\Http\UploadedFile) {
                    continue;
                }

                if (!$archivo->isValid()) {
                    throw new \RuntimeException(
                        'Una fotografía no se recibió correctamente.'
                    );
                }

                $ruta = $archivo->store(
                    'ordenes/' . $orden->id . '/diagnostico',
                    'public'
                );


                
                if (!$ruta) {
                throw new \RuntimeException('Falló el almacenamiento de la fotografía.');
            }

            logger()->info('Foto de diagnóstico almacenada', [
                'ruta' => $ruta,
                'existe' => Storage::disk('public')->exists($ruta),
                'detalle_id' => $detalleDiagnostico->id,
            ]);

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

        logger()->info('Verificando avance de diagnóstico', [
    'orden_id' => $orden->id,
    'estado_actual' => $orden->estado,
    'diagnostico_id' => $diagnostico->id,
    'tecnico_id' => $diagnostico->tecnico_id,
]);
        // Solo el registro inicial del diagnóstico avanza de etapa.
        if ($orden->estado === 'EN_DIAGNOSTICO') {
            $orden->estado = 'EN_COTIZACION';
            $orden->save();
        }

        $orden->usuario_modificador_id = Auth::id();
        $orden->save();

        DB::commit();

        return redirect()
            ->route('ordenServicio.detalle', $orden->id)
            ->with(
                'success',
                $esNuevo
                    ? 'Diagnóstico registrado correctamente.'
                    : 'Diagnóstico actualizado correctamente.'
            );

    } catch (\Throwable $e) {
    DB::rollBack();

    if (!empty($archivosGuardados)) {
        Storage::disk('public')->delete($archivosGuardados);
    }

    logger()->error('Error al guardar diagnóstico', [
        'orden_id' => $id,
        'usuario_id' => Auth::id(),
        'mensaje' => $e->getMessage(),
        'archivo' => $e->getFile(),
        'linea' => $e->getLine(),
        'excepcion' => get_class($e),
    ]);

    report($e);

    return back()
        ->withInput()
        ->with(
            'error',
            'No se pudo guardar el diagnóstico: ' . $e->getMessage()
        );
}
}

}
