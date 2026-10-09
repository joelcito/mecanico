<?php

namespace App\Services;

use App\Models\MovimientoInventario;
use App\Models\OrdenServicio;
use App\Models\Pago;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ReportesService
{
    public const TIPOS = ['ventas', 'movimientos', 'productos', 'pagos'];

    public function columnas(string $tipo): array
    {
        return match ($tipo) {
            'ventas' => [
                'orden' => 'Orden',
                'fecha' => 'Fecha de orden',
                'cliente' => 'Cliente',
                'vehiculo' => 'Vehículo',
                'placa' => 'Placa',
                'total' => 'Total cotizado (Bs.)',
                'estado' => 'Estado',
            ],
            'movimientos' => [
                'fecha' => 'Fecha',
                'producto' => 'Producto',
                'tipo' => 'Tipo',
                'motivo' => 'Motivo',
                'cantidad' => 'Cantidad',
                'stock_anterior' => 'Stock anterior',
                'stock_nuevo' => 'Stock nuevo',
                'sucursal' => 'Sucursal atribuida',
                'usuario' => 'Usuario',
            ],
            'productos' => [
                'codigo' => 'Código',
                'producto' => 'Producto',
                'unidad' => 'Unidad',
                'stock_actual' => 'Stock global actual',
                'stock_minimo' => 'Stock mínimo',
                'entradas' => 'Entradas del período',
                'salidas' => 'Salidas del período',
                'neto' => 'Cambio neto del período',
            ],
            'pagos' => [
                'fecha' => 'Fecha de pago',
                'orden' => 'Orden',
                'sucursal' => 'Sucursal',
                'tipo_pago' => 'Método',
                'monto' => 'Monto (Bs.)',
                'estado' => 'Estado',
                'usuario' => 'Registrado por',
            ],
            default => [],
        };
    }

    public function titulo(string $tipo): string
    {
        return match ($tipo) {
            'ventas' => 'Reporte de Ventas',
            'movimientos' => 'Reporte de Movimientos de Inventario',
            'productos' => 'Reporte de Productos / Stock',
            'pagos' => 'Reporte de Pagos',
            default => 'Reporte',
        };
    }

    public function consulta(string $tipo, array $filtros, array $sucursalesPermitidas, bool $administrador): Builder|QueryBuilder
    {
        $desde = $filtros['desde']->copy()->startOfDay();
        $hasta = $filtros['hasta']->copy()->endOfDay();
        $sucursalId = $filtros['sucursal_id'];
        $filtrarSucursales = !$administrador || $sucursalId !== null;
        $ids = $sucursalId !== null ? [$sucursalId] : $sucursalesPermitidas;

        if ($tipo === 'ventas') {
            $consulta = OrdenServicio::query()
                ->with(['vehiculo.marca', 'vehiculo.cliente.user', 'cotizacionActual', 'usuarioCreador'])
                ->whereBetween('fecha_recepcion', [$desde, $hasta])
                ->whereHas('cotizacionActual', fn ($query) => $query->where('estado', 'APROBADA'));

            if ($filtrarSucursales) {
                $consulta->whereHas('usuarioCreador', fn ($query) => $query->whereIn('sucursal_id', $ids));
            }

            return $consulta->orderByDesc('fecha_recepcion');
        }

        if ($tipo === 'movimientos') {
            $consulta = MovimientoInventario::query()
                ->with(['producto', 'usuarioCreador.sucursal', 'ordenServicio.usuarioCreador.sucursal'])
                ->whereBetween('fecha', [$desde, $hasta]);

            if ($filtrarSucursales) {
                $consulta->where(function ($query) use ($ids) {
                    $query->whereHas('usuarioCreador', fn ($usuario) => $usuario->whereIn('sucursal_id', $ids))
                        ->orWhere(function ($sinCreador) use ($ids) {
                            $sinCreador->whereNull('usuario_creador_id')
                                ->whereHas('ordenServicio.usuarioCreador', fn ($usuario) => $usuario->whereIn('sucursal_id', $ids));
                        });
                });
            }

            return $consulta->orderByDesc('fecha')->orderByDesc('id');
        }

        if ($tipo === 'pagos') {
            $consulta = Pago::query()
                ->with(['ordenServicio', 'sucursal', 'usuarioCreador'])
                ->whereBetween('fecha', [$desde, $hasta]);

            if ($filtrarSucursales) {
                $consulta->whereIn('sucursal_id', $ids);
            }

            return $consulta->orderByDesc('fecha')->orderByDesc('id');
        }

        $movimientosPeriodo = MovimientoInventario::query()
            ->select('producto_id')
            ->selectRaw("SUM(CASE WHEN tipo = 'ENTRADA' THEN cantidad ELSE 0 END) AS entradas_periodo")
            ->selectRaw("SUM(CASE WHEN tipo = 'SALIDA' THEN cantidad ELSE 0 END) AS salidas_periodo")
            ->whereBetween('fecha', [$desde, $hasta]);

        if ($filtrarSucursales) {
            $movimientosPeriodo->where(function ($query) use ($ids) {
                $query->whereHas('usuarioCreador', fn ($usuario) => $usuario->whereIn('sucursal_id', $ids))
                    ->orWhere(function ($sinCreador) use ($ids) {
                        $sinCreador->whereNull('usuario_creador_id')
                            ->whereHas('ordenServicio.usuarioCreador', fn ($usuario) => $usuario->whereIn('sucursal_id', $ids));
                    });
            });
        }

        $subconsulta = $movimientosPeriodo->groupBy('producto_id');

        return Producto::query()
            ->leftJoinSub($subconsulta, 'movimientos_periodo', function ($join) {
                $join->on('productos.id', '=', 'movimientos_periodo.producto_id');
            })
            ->whereNull('productos.deleted_at')
            ->where('productos.estado', 'ACTIVO')
            ->whereHas('categoria', fn ($query) => $query->where('tipo', 'PRODUCTO'))
            ->select([
                'productos.id',
                'productos.codigo',
                'productos.nombre',
                'productos.unidad_medida',
                'productos.cantidad',
                'productos.stock_minimo',
            ])
            ->selectRaw('COALESCE(movimientos_periodo.entradas_periodo, 0) AS entradas_periodo')
            ->selectRaw('COALESCE(movimientos_periodo.salidas_periodo, 0) AS salidas_periodo')
            ->orderBy('productos.nombre');
    }

    public function fila(string $tipo, object $registro): array
    {
        return match ($tipo) {
            'ventas' => [
                'orden' => $registro->numero_orden,
                'fecha' => $registro->fecha_recepcion?->format('d/m/Y H:i') ?? '-',
                'cliente' => $this->nombreCompleto($registro->vehiculo?->cliente?->user),
                'vehiculo' => trim(($registro->vehiculo?->marca?->nombre ?? '') . ' ' . ($registro->vehiculo?->modelo ?? '')) ?: '-',
                'placa' => $registro->vehiculo?->placa ?? '-',
                'total' => number_format((float) ($registro->cotizacionActual?->total ?? 0), 2, ',', '.'),
                'estado' => $registro->estado,
            ],
            'movimientos' => [
                'fecha' => $registro->fecha?->format('d/m/Y H:i') ?? '-',
                'producto' => $registro->producto?->nombre ?? 'Producto eliminado',
                'tipo' => $registro->tipo,
                'motivo' => $registro->motivo,
                'cantidad' => number_format((float) $registro->cantidad, 2, ',', '.'),
                'stock_anterior' => number_format((float) $registro->stock_anterior, 2, ',', '.'),
                'stock_nuevo' => number_format((float) $registro->stock_nuevo, 2, ',', '.'),
                'sucursal' => $registro->usuarioCreador?->sucursal?->nombre
                    ?? $registro->ordenServicio?->usuarioCreador?->sucursal?->nombre
                    ?? 'Sin sucursal atribuible',
                'usuario' => $this->nombreCompleto($registro->usuarioCreador),
            ],
            'productos' => [
                'codigo' => $registro->codigo ?? '-',
                'producto' => $registro->nombre ?? '-',
                'unidad' => $registro->unidad_medida ?? '-',
                'stock_actual' => number_format((float) $registro->cantidad, 2, ',', '.'),
                'stock_minimo' => number_format((float) $registro->stock_minimo, 2, ',', '.'),
                'entradas' => number_format((float) $registro->entradas_periodo, 2, ',', '.'),
                'salidas' => number_format((float) $registro->salidas_periodo, 2, ',', '.'),
                'neto' => number_format((float) $registro->entradas_periodo - (float) $registro->salidas_periodo, 2, ',', '.'),
            ],
            'pagos' => [
                'fecha' => $registro->fecha?->format('d/m/Y H:i') ?? '-',
                'orden' => $registro->ordenServicio?->numero_orden ?? '-',
                'sucursal' => $registro->sucursal?->nombre ?? 'Sin sucursal',
                'tipo_pago' => $registro->tipo_pago,
                'monto' => number_format((float) $registro->monto, 2, ',', '.'),
                'estado' => $registro->estado,
                'usuario' => $this->nombreCompleto($registro->usuarioCreador),
            ],
            default => [],
        };
    }

    private function nombreCompleto(?object $usuario): string
    {
        return $usuario
            ? (trim(($usuario->nombres ?? '') . ' ' . ($usuario->ap_paterno ?? '') . ' ' . ($usuario->ap_materno ?? '')) ?: '-')
            : '-';
    }
}