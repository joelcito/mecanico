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
    /**
     * Mostrar formulario de inspección.
     */
    public function crear($id)
{
    $orden = OrdenServicio::with([
        'vehiculo.cliente.user',
        'vehiculo.marca',
        'inspeccionActual.detalles',
        'inspeccionActual.fotos',
    ])->findOrFail($id);

    if (!in_array($orden->estado, [
        'RECIBIDO',
        'EN_INSPECCION',
    ])) {
        return redirect()
            ->route(
                'ordenServicio.detalle',
                $orden->id
            )
            ->with(
                'error',
                'La orden no se encuentra disponible para inspección.'
            );
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
        ->get();

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
    try {

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

        $orden = OrdenServicio::findOrFail($id);

        if (!in_array($orden->estado, [
            'RECIBIDO',
            'EN_INSPECCION',
        ])) {
            throw new \Exception(
                'La orden no se encuentra disponible para inspección.'
            );
        }

        $inspeccion = OrdenInspeccion::create([
            'orden_servicio_id' => $orden->id,
            'usuario_inspector_id' => Auth::id(),
            'fecha' => now(),
            'observaciones' => $request->observaciones,
            'estado' => 'FINALIZADA',
            'usuario_creador_id' => Auth::id(),
        ]);

        foreach ($request->items as $checklistId => $datos) {

            $checklistItem = ChecklistItem::find($checklistId);

            if (!$checklistItem) {
                continue;
            }

            OrdenInspeccionDetalle::create([
                'orden_inspeccion_id' => $inspeccion->id,
                'item' => $checklistItem->nombre,
                'resultado' => $datos['resultado'],
                'observacion' => $datos['observacion'] ?? null,
                'estado' => 'ACTIVO',
                'usuario_creador_id' => Auth::id(),
            ]);
        }

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

        $orden->update([
            'estado' => 'EN_DIAGNOSTICO',
            'usuario_modificador_id' => Auth::id(),
        ]);

        DB::commit();

        return redirect()
            ->route('ordenServicio.detalle', $orden->id)
            ->with(
                'success',
                'Inspección registrada correctamente.'
            );

    } catch (\Throwable $e) {

        DB::rollBack();

        return back()
            ->withInput()
            ->with(
                'error',
                'ERROR: ' . $e->getMessage()
            );
    }
}
    
}