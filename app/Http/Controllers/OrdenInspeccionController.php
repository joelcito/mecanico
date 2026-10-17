<?php

namespace App\Http\Controllers;

use App\Models\OrdenServicio;
use App\Models\OrdenInspeccion;
use App\Models\OrdenInspeccionDetalle;
use App\Models\OrdenFoto;
use App\Utils\Respuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\ChecklistItem;

class OrdenInspeccionController extends Controller
{
    public function crear($id)
{
    $orden = OrdenServicio::with([
        'vehiculo.cliente.user',
        'vehiculo.marca',
        'inspeccionActual.detalles',
        'inspeccionActual.fotos',
    ])->findOrFail($id);

            
        $estadosPermitidos = [
            'RECIBIDO',
            'EN_INSPECCION',
        ];

        if (
            $orden->estado === 'EN_DIAGNOSTICO'
            && $orden->inspeccionActual
        ) {
            $estadosPermitidos[] = 'EN_DIAGNOSTICO';
        }

        if (!in_array($orden->estado, $estadosPermitidos)) {
            return redirect()
                ->route('ordenServicio.detalle', $orden->id)
                ->with('error', 'La inspección ya no se puede editar en esta etapa.');
        }


    $vehiculo = $orden->vehiculo;
    $items = ChecklistItem::where(
        'tipo_vehiculo',
        $vehiculo->tipo_vehiculo
    )
    ->where('estado', 'ACTIVO')
    ->where(function ($query) use ($vehiculo) {
        $query->whereNull('tipo_propulsion')
            ->orWhere(
                'tipo_propulsion',
                $vehiculo->tipo_propulsion
            );

    })
    ->orderBy('orden')
    ->get()
    ->unique('nombre')
    ->values();
    return view(
        'ordenServicio.inspeccion',
        compact(
            'orden',
            'items'
        )
    );
}

  
public function guardar(Request $request, $id)
{
    $request->validate([
        'observaciones' => 'nullable|string',
        'items' => 'required|array',
        'items.*.resultado' => 'required|in:SI,NO,NA',
        'items.*.observacion' => 'nullable|string',
        'fotos' => 'nullable|array',
        'fotos.*' => [
            'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:5120',
        ],
    ]);

    DB::beginTransaction();

    try {
        $orden = OrdenServicio::whereKey($id)
            ->lockForUpdate()
            ->firstOrFail();

        // Solo permitir inspección inicial o edición durante diagnóstico.
        if (!in_array($orden->estado, [
            'RECIBIDO',
            'EN_INSPECCION',
            'EN_DIAGNOSTICO',
        ])) {
            throw new \Exception(
                'La inspección no se puede modificar en esta etapa.'
            );
        }

        $inspeccion = OrdenInspeccion::where(
            'orden_servicio_id',
            $orden->id
        )->latest('id')->first();

        $esNueva = !$inspeccion;

        if ($esNueva) {
            $inspeccion = new OrdenInspeccion();
            $inspeccion->orden_servicio_id = $orden->id;
            $inspeccion->usuario_inspector_id = Auth::id();
            $inspeccion->fecha = now();
            $inspeccion->estado = 'FINALIZADA';
            $inspeccion->usuario_creador_id = Auth::id();
        }

        $inspeccion->observaciones = $request->observaciones;
        $inspeccion->usuario_modificador_id = Auth::id();
        $inspeccion->save();

        foreach ($request->items as $checklistId => $datos) {
            $checklistItem = ChecklistItem::find($checklistId);

            if (!$checklistItem) {
                continue;
            }

            // Buscar el detalle existente de este elemento.
            $detalle = OrdenInspeccionDetalle::where(
                'orden_inspeccion_id',
                $inspeccion->id
            )->where(
                'item',
                $checklistItem->nombre
            )->first();

            if (!$detalle) {
                $detalle = new OrdenInspeccionDetalle();
                $detalle->orden_inspeccion_id = $inspeccion->id;
                $detalle->item = $checklistItem->nombre;
                $detalle->estado = 'ACTIVO';
                $detalle->usuario_creador_id = Auth::id();
            }

            $detalle->resultado = $datos['resultado'];
            $detalle->observacion = $datos['observacion'] ?? null;
            $detalle->usuario_modificador_id = Auth::id();
            $detalle->save();
        }

        // Las fotografías nuevas se agregan sin eliminar las anteriores.
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $foto) {
                if (!$foto) {
                    continue;
                }

                $ruta = $foto->store(
                    'ordenes/' . $orden->id . '/inspeccion',
                    'public'
                );

                OrdenFoto::create([
                    'orden_servicio_id' => $orden->id,
                    'orden_inspeccion_id' => $inspeccion->id,
                    'tipo' => 'INSPECCION',
                    'ruta' => $ruta,
                    'descripcion' => null,
                    'estado' => 'ACTIVO',
                    'usuario_creador_id' => Auth::id(),
                ]);
            }
        }

        // Cambiar de etapa solamente al registrar la inspección inicial.
        if ($esNueva && in_array($orden->estado, [
            'RECIBIDO',
            'EN_INSPECCION',
        ])) {
            $orden->estado = 'EN_DIAGNOSTICO';
        }

        $orden->usuario_modificador_id = Auth::id();
        $orden->save();

        DB::commit();

        return redirect()
            ->route('ordenServicio.detalle', $orden->id)
            ->with(
                'success',
                $esNueva
                    ? 'Inspección registrada correctamente.'
                    : 'Inspección actualizada correctamente.'
            );

    } catch (\Throwable $e) {
        DB::rollBack();

        report($e);

        return back()
            ->withInput()
            ->with(
                'error',
                'No se pudo guardar la inspección. Verifica los datos e inténtalo nuevamente.'
            );
    }
}

    

public function eliminarFoto($ordenId, $fotoId)
{
    $ruta = DB::transaction(function () use ($ordenId, $fotoId) {
        $orden = OrdenServicio::whereKey($ordenId)
            ->lockForUpdate()
            ->firstOrFail();

        if (!in_array($orden->estado, [
            'RECIBIDO',
            'EN_INSPECCION',
            'EN_DIAGNOSTICO',
        ])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'foto' => 'No se pueden eliminar fotos en esta etapa de la orden.',
            ]);
        }

        $inspeccion = OrdenInspeccion::where('orden_servicio_id', $orden->id)
            ->latest('id')
            ->firstOrFail();

        $foto = OrdenFoto::where('orden_servicio_id', $orden->id)
            ->where('orden_inspeccion_id', $inspeccion->id)
            ->where('tipo', 'INSPECCION')
            ->lockForUpdate()
            ->findOrFail($fotoId);

        $foto->usuario_eliminador_id = Auth::id();
        $foto->save();

        $ruta = $foto->ruta;

        // Eliminación lógica gracias a SoftDeletes.
        $foto->delete();

        return $ruta;
    });

    Storage::disk('public')->delete($ruta);

    return response()->json([
        'estado' => true,
        'message' => 'La fotografía fue eliminada correctamente.',
    ]);
}


}