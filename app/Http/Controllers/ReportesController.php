<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use App\Services\ReportesService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;

class ReportesController extends Controller
{
    public function index(Request $request, string $tipo, ReportesService $reportes)
    {
        abort_unless(in_array($tipo, ReportesService::TIPOS, true), 404);

        [$filtros, $sucursales, $administrador, $idsPermitidos] = $this->filtros($request);
        $query = $reportes->consulta($tipo, $filtros, $idsPermitidos, $administrador);
        $registros = $query->paginate(50)->withQueryString();
        $registros->getCollection()->transform(fn ($registro) => $reportes->fila($tipo, $registro));

        return view('reportes.index', [
            'tipo' => $tipo,
            'titulo' => $reportes->titulo($tipo),
            'columnas' => $reportes->columnas($tipo),
            'registros' => $registros,
            'filtros' => $filtros,
            'sucursales' => $sucursales,
            'etiquetaSucursal' => $filtros['sucursal']?->nombre ?? 'Todas',
        ]);
    }

    public function exportar(Request $request, string $tipo, string $formato, ReportesService $reportes)
    {
        abort_unless(in_array($tipo, ReportesService::TIPOS, true), 404);
        abort_unless(in_array($formato, ['pdf', 'xlsx'], true), 404);

        [$filtros, , $administrador, $idsPermitidos] = $this->filtros($request);
        $columnas = $reportes->columnas($tipo);
        $registros = $reportes->consulta($tipo, $filtros, $idsPermitidos, $administrador)
            ->get()
            ->map(fn ($registro) => $reportes->fila($tipo, $registro));
        $titulo = $reportes->titulo($tipo);
        $sucursal = $filtros['sucursal']?->nombre ?? 'Todas';
        $periodo = $filtros['desde']->format('d/m/Y') . ' al ' . $filtros['hasta']->format('d/m/Y');
        $nombreArchivo = 'reporte-' . $tipo . '-' . $filtros['desde']->format('Ymd') . '-' . $filtros['hasta']->format('Ymd');

        if ($formato === 'pdf') {
            return Pdf::loadView('reportes.pdf', compact(
                'titulo',
                'columnas',
                'registros',
                'periodo',
                'sucursal',
                'tipo'
            ))->setPaper('a4', 'landscape')->stream($nombreArchivo . '.pdf');
        }

        $archivoTemporal = tempnam(sys_get_temp_dir(), 'reporte_');
        $writer = new Writer();
        $writer->openToFile($archivoTemporal);
        $writer->addRow(Row::fromValues([$titulo]));
        $writer->addRow(Row::fromValues(['Período', $periodo]));
        $writer->addRow(Row::fromValues(['Sucursal', $sucursal]));
        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues(array_values($columnas)));

        foreach ($registros as $registro) {
            $writer->addRow(Row::fromValues(array_values($registro)));
        }
        $writer->close();
        return response()->download($archivoTemporal, $nombreArchivo . '.xlsx')->deleteFileAfterSend(true);
    }

    private function filtros(Request $request): array
    {
        $usuario = Auth::user();
        $administrador = $usuario->rol?->nombre === 'Administrador';
        $sucursales = Sucursal::query()
            ->where('estado', 'ACTIVO')
            ->whereNull('deleted_at')
            ->when(!$administrador, fn ($query) => $query->where('id', $usuario->sucursal_id))
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        $idsPermitidos = $sucursales->pluck('id')->map(fn ($id) => (int) $id)->all();
        if (!$administrador && $sucursales->isEmpty()) {
            abort(403, 'El usuario no tiene una sucursal activa asignada.');
        }

        $hoy = now()->toDateString();
        $datos = $request->validate([
            'fecha_desde' => ['sometimes', 'required', 'date_format:Y-m-d'],
            'fecha_hasta' => [
                'sometimes',
                'required',
                'date_format:Y-m-d',
                'after_or_equal:fecha_desde',
            ],
            'sucursal_id' => [
                'nullable',
                'integer',
                Rule::exists('sucursales', 'id')
                    ->where('estado', 'ACTIVO')
                    ->whereNull('deleted_at'),
            ],
        ], [
            'fecha_desde.date_format' => 'La fecha desde no es válida.',
            'fecha_hasta.date_format' => 'La fecha hasta no es válida.',
            'fecha_hasta.after_or_equal' => 'La fecha hasta debe ser mayor o igual a la fecha desde.',
            'sucursal_id.exists' => 'La sucursal seleccionada no existe o está inactiva.',
        ]);

        $desde = Carbon::createFromFormat('!Y-m-d', $datos['fecha_desde'] ?? now()->startOfMonth()->toDateString());
        $hasta = Carbon::createFromFormat('!Y-m-d', $datos['fecha_hasta'] ?? $hoy);
        $sucursalId = filled($datos['sucursal_id'] ?? null) ? (int) $datos['sucursal_id'] : null;
        $sucursal = null;

        if ($sucursalId !== null) {
            abort_unless(in_array($sucursalId, $idsPermitidos, true), 403, 'No tiene autorización para consultar esa sucursal.');
            $sucursal = $sucursales->firstWhere('id', $sucursalId);
        }

        return [[
            'desde' => $desde,
            'hasta' => $hasta,
            'desde_input' => $desde->format('Y-m-d'),
            'hasta_input' => $hasta->format('Y-m-d'),
            'sucursal_id' => $sucursalId,
            'sucursal' => $sucursal,
        ], $sucursales, $administrador, $idsPermitidos];
    }
}