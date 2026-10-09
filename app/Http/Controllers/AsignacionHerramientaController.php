<?php

namespace App\Http\Controllers;

use App\Models\AsignacionHerramienta;
use App\Models\Herramienta;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AsignacionHerramientaController extends Controller
{
    private const ROLES_ADMINISTRADORES = [1, 3];

    public function index(Request $request)
    {
        $usuario = Auth::user();
        $esTecnico = (int) $usuario->rol_id === 2;
        abort_unless($esTecnico || in_array((int) $usuario->rol_id, self::ROLES_ADMINISTRADORES, true), 403);

        $filtros = $request->validate([
            'tecnico_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('rol_id', 2)),
            ],
            'herramienta_id' => ['nullable', 'integer', 'exists:herramientas,id'],
            'estado' => ['nullable', 'in:TODAS,ASIGNADA,DEVUELTA'],
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
        ]);

        $consulta = AsignacionHerramienta::with([
            'herramienta',
            'tecnico',
            'usuarioAsignador',
            'usuarioDevolucion',
        ])->orderByDesc('fecha_asignacion')->orderByDesc('id');

        if ($esTecnico) {
            $consulta->where('mecanico_id', $usuario->id);
        } elseif (!empty($filtros['tecnico_id'])) {
            $consulta->where('mecanico_id', $filtros['tecnico_id']);
        }

        if (!empty($filtros['herramienta_id'])) {
            $consulta->where('herramienta_id', $filtros['herramienta_id']);
        }

        $estado = $filtros['estado'] ?? ($esTecnico ? 'ASIGNADA' : 'TODAS');
        if ($estado !== 'TODAS') {
            $consulta->where('estado', $estado);
        }
        if (!empty($filtros['desde'])) {
            $consulta->whereDate('fecha_asignacion', '>=', $filtros['desde']);
        }
        if (!empty($filtros['hasta'])) {
            $consulta->whereDate('fecha_asignacion', '<=', $filtros['hasta']);
        }

        $asignaciones = $consulta->get();
        $tecnicos = $esTecnico
            ? collect([$usuario])
            : User::where('rol_id', 2)
                ->where('estado', 'ACTIVO')
                ->orderBy('nombres')
                ->get();
        $herramientas = Herramienta::whereNull('deleted_at')
            ->where('estado', 'ACTIVO')
            ->where('estado_disponibilidad', 'DISPONIBLE')
            ->orderBy('nombre')
            ->get(['id', 'codigo', 'nombre', 'estado_disponibilidad']);
        $consultaHerramientasHistorial = Herramienta::whereNull('deleted_at')
            ->with('asignacionActiva.tecnico')
            ->orderBy('nombre')
            ->select(['id', 'codigo', 'nombre', 'estado', 'estado_disponibilidad']);
        if ($esTecnico) {
            $consultaHerramientasHistorial->whereHas('asignaciones', function ($query) use ($usuario) {
                $query->where('mecanico_id', $usuario->id);
            });
        }
        $todasHerramientas = $consultaHerramientasHistorial->get();

        $resumen = null;
        if (!$esTecnico) {
            $herramientasCatalogo = Herramienta::whereNull('deleted_at');
            $resumen = [
                'total' => (clone $herramientasCatalogo)->count(),
                'disponibles' => (clone $herramientasCatalogo)->where('estado_disponibilidad', 'DISPONIBLE')->count(),
                'asignadas' => (clone $herramientasCatalogo)->where('estado_disponibilidad', 'ASIGNADA')->count(),
                'mantenimiento' => (clone $herramientasCatalogo)->where('estado_disponibilidad', 'MANTENIMIENTO')->count(),
            ];
        }

        return view('herramienta.asignaciones', compact(
            'asignaciones',
            'tecnicos',
            'herramientas',
            'todasHerramientas',
            'resumen',
            'esTecnico',
            'filtros',
            'estado'
        ));
    }

    public function porTecnico($tecnicoId)
    {
        $this->asegurarAdministrador();
        $tecnico = User::where('rol_id', 2)->findOrFail($tecnicoId);

        return redirect()->route('asignacionesHerramientas.listado', [
            'tecnico_id' => $tecnico->id,
            'estado' => 'TODAS',
        ]);
    }

    public function historialHerramienta($herramientaId)
    {
        $usuario = Auth::user();
        abort_unless(
            (int) $usuario->rol_id === 2 || in_array((int) $usuario->rol_id, self::ROLES_ADMINISTRADORES, true),
            403
        );
        $herramienta = Herramienta::findOrFail($herramientaId);
        $consulta = $herramienta->asignaciones()
            ->with(['tecnico', 'usuarioAsignador', 'usuarioDevolucion'])
            ->orderByDesc('fecha_asignacion');
        if ((int) $usuario->rol_id === 2) {
            $consulta->where('mecanico_id', $usuario->id);
        }

        return response()->json([
            'estado' => true,
            'data' => [
                'herramienta' => $herramienta,
                'asignaciones' => $consulta->get(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $this->asegurarAdministrador();
        $datos = $request->validate([
            'tecnico_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query
                    ->where('rol_id', 2)
                    ->where('estado', 'ACTIVO')
                    ->whereNull('deleted_at')),
            ],
            'herramienta_id' => [
                'required',
                'integer',
                Rule::exists('herramientas', 'id')->where(fn ($query) => $query
                    ->where('estado', 'ACTIVO')
                    ->whereNull('deleted_at')),
            ],
            'fecha_asignacion' => ['required', 'date'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
        ]);

        $asignacion = DB::transaction(function () use ($datos) {
            $tecnico = User::where('rol_id', 2)
                ->where('estado', 'ACTIVO')
                ->lockForUpdate()
                ->findOrFail($datos['tecnico_id']);
            $herramienta = Herramienta::whereNull('deleted_at')
                ->lockForUpdate()
                ->findOrFail($datos['herramienta_id']);

            if ($herramienta->estado !== 'ACTIVO') {
                throw ValidationException::withMessages([
                    'herramienta_id' => 'La herramienta no está activa en el catálogo.',
                ]);
            }

            $asignacionActiva = AsignacionHerramienta::where('herramienta_id', $herramienta->id)
                ->whereIn('estado', ['ASIGNADA', 'ACTIVA'])
                ->exists();
            if ($asignacionActiva || $herramienta->estado_disponibilidad === 'ASIGNADA') {
                throw ValidationException::withMessages([
                    'herramienta_id' => 'La herramienta ya se encuentra asignada a otro técnico.',
                ]);
            }
            if ($herramienta->estado_disponibilidad !== 'DISPONIBLE') {
                throw ValidationException::withMessages([
                    'herramienta_id' => 'La herramienta no está disponible para asignación.',
                ]);
            }

            $asignacion = AsignacionHerramienta::create([
                'producto_id' => null,
                'herramienta_id' => $herramienta->id,
                'reparacion_id' => null,
                'mecanico_id' => $tecnico->id,
                'cantidad' => 1,
                'fecha_asignacion' => $datos['fecha_asignacion'],
                'estado' => 'ASIGNADA',
                'observaciones' => $datos['observaciones'] ?? null,
                'usuario_creador_id' => Auth::id(),
            ]);

            $herramienta->update([
                'estado_disponibilidad' => 'ASIGNADA',
                'usuario_modificador_id' => Auth::id(),
            ]);

            return $asignacion->load(['herramienta', 'tecnico', 'usuarioAsignador']);
        });

        return response()->json([
            'estado' => true,
            'message' => 'Herramienta asignada correctamente.',
            'data' => $asignacion,
        ], 201);
    }

    public function devolver(Request $request, $id)
    {
        $this->asegurarAdministrador();
        $datos = $request->validate([
            'observaciones_devolucion' => ['nullable', 'string', 'max:2000'],
        ]);

        $resultado = DB::transaction(function () use ($id, $datos) {
            $asignacion = AsignacionHerramienta::lockForUpdate()->findOrFail($id);
            if (!in_array($asignacion->estado, ['ASIGNADA', 'ACTIVA'], true)) {
                throw ValidationException::withMessages([
                    'asignacion' => 'Esta herramienta ya fue devuelta.',
                ]);
            }
            $herramienta = Herramienta::whereNull('deleted_at')
                ->lockForUpdate()
                ->findOrFail($asignacion->herramienta_id);

            $asignacion->update([
                'estado' => 'DEVUELTA',
                'fecha_devolucion' => now(),
                'usuario_devolucion_id' => Auth::id(),
                'usuario_modificador_id' => Auth::id(),
                'observaciones' => $this->combinarObservaciones(
                    $asignacion->observaciones,
                    $datos['observaciones_devolucion'] ?? null
                ),
            ]);
            $otraAsignacionActiva = AsignacionHerramienta::where('herramienta_id', $herramienta->id)
                ->where('id', '!=', $asignacion->id)
                ->whereIn('estado', ['ASIGNADA', 'ACTIVA'])
                ->exists();
            $herramienta->update([
                'estado_disponibilidad' => $otraAsignacionActiva ? 'ASIGNADA' : 'DISPONIBLE',
                'usuario_modificador_id' => Auth::id(),
            ]);

            return [
                'asignacion' => $asignacion->load(['herramienta', 'tecnico', 'usuarioAsignador', 'usuarioDevolucion']),
                'disponibilidad' => $herramienta->estado_disponibilidad,
            ];
        });

        return response()->json([
            'estado' => true,
            'message' => $resultado['disponibilidad'] === 'DISPONIBLE'
                ? 'La herramienta fue devuelta y está disponible nuevamente.'
                : 'La devolución se registró; otra asignación activa aún mantiene la herramienta ocupada.',
            'data' => $resultado['asignacion'],
        ]);
    }

    public function actualizarEstado(Request $request, $herramientaId)
    {
        $this->asegurarAdministrador();
        $datos = $request->validate([
            'estado_disponibilidad' => ['required', Rule::in(['DISPONIBLE', 'MANTENIMIENTO', 'BAJA'])],
        ]);

        $herramienta = DB::transaction(function () use ($herramientaId, $datos) {
            $herramienta = Herramienta::whereNull('deleted_at')
                ->lockForUpdate()
                ->findOrFail($herramientaId);

            if ($herramienta->estado !== 'ACTIVO' && $datos['estado_disponibilidad'] === 'DISPONIBLE') {
                throw ValidationException::withMessages([
                    'estado_disponibilidad' => 'Active la herramienta en el catálogo antes de marcarla disponible.',
                ]);
            }

            $asignacionActiva = AsignacionHerramienta::where('herramienta_id', $herramienta->id)
                ->whereIn('estado', ['ASIGNADA', 'ACTIVA'])
                ->exists();
            if ($asignacionActiva || $herramienta->estado_disponibilidad === 'ASIGNADA') {
                throw ValidationException::withMessages([
                    'estado_disponibilidad' => 'Devuelva la herramienta antes de cambiar su disponibilidad.',
                ]);
            }

            $herramienta->update([
                'estado_disponibilidad' => $datos['estado_disponibilidad'],
                'usuario_modificador_id' => Auth::id(),
            ]);

            return $herramienta;
        });

        return response()->json([
            'estado' => true,
            'message' => 'Disponibilidad actualizada.',
            'data' => $herramienta,
        ]);
    }

    private function asegurarAdministrador(): void
    {
        abort_unless(in_array((int) Auth::user()->rol_id, self::ROLES_ADMINISTRADORES, true), 403);
    }

    private function combinarObservaciones(?string $actual, ?string $devolucion): ?string
    {
        if (!$devolucion) {
            return $actual;
        }

        $notaDevolucion = 'Devolución: ' . trim($devolucion);
        return trim(($actual ? $actual . PHP_EOL . PHP_EOL : '') . $notaDevolucion);
    }
}