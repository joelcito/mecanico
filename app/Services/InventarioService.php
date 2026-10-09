<?php

namespace App\Services;

use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventarioService
{
    public function registrarSalida(int $productoId, float $cantidad, array $contexto = []): MovimientoInventario
    {
        return $this->registrarMovimiento($productoId, $cantidad, 'SALIDA', 'REPARACION', $contexto);
    }

    public function registrarDevolucion(int $productoId, float $cantidad, array $contexto = []): MovimientoInventario
    {
        return $this->registrarMovimiento(
            $productoId,
            $cantidad,
            'ENTRADA',
            'DEVOLUCION_REPARACION',
            $contexto,
            true
        );
    }

    public function registrarIngreso(int $productoId, float $cantidad, string $motivo, ?string $observaciones = null): MovimientoInventario
    {
        return $this->registrarMovimiento($productoId, $cantidad, 'ENTRADA', $motivo, [
            'observaciones' => $observaciones,
        ]);
    }

    public function registrarSalidaManual(int $productoId, float $cantidad, string $motivo, ?string $observaciones = null): MovimientoInventario
    {
        return $this->registrarMovimiento($productoId, $cantidad, 'SALIDA', $motivo, [
            'observaciones' => $observaciones,
        ]);
    }

    private function registrarMovimiento(
        int $productoId,
        float $cantidad,
        string $tipo,
        string $motivo,
        array $contexto,
        bool $permitirProductoInactivo = false
    ): MovimientoInventario {
        return DB::transaction(function () use ($productoId, $cantidad, $tipo, $motivo, $contexto, $permitirProductoInactivo) {
            $producto = Producto::lockForUpdate()->findOrFail($productoId);

            if (!$permitirProductoInactivo
                && ($producto->estado !== 'ACTIVO' || $producto->deleted_at !== null)) {
                throw ValidationException::withMessages([
                    'producto_id' => 'El producto se encuentra inactivo.',
                ]);
            }

            if ($cantidad <= 0) {
                throw ValidationException::withMessages([
                    'cantidad' => 'La cantidad debe ser mayor que cero.',
                ]);
            }

            $stockAnterior = (float) $producto->cantidad;
            if ($tipo === 'SALIDA' && $cantidad > $stockAnterior) {
                throw ValidationException::withMessages([
                    'cantidad' => sprintf(
                        'Stock insuficiente. Disponible: %s %s; solicitado: %s %s.',
                        rtrim(rtrim(number_format($stockAnterior, 2, '.', ''), '0'), '.'),
                        $producto->unidad_medida,
                        rtrim(rtrim(number_format($cantidad, 2, '.', ''), '0'), '.'),
                        $producto->unidad_medida
                    ),
                ]);
            }

            $stockNuevo = $tipo === 'SALIDA'
                ? $stockAnterior - $cantidad
                : $stockAnterior + $cantidad;

            $movimiento = MovimientoInventario::create([
                'producto_id' => $producto->id,
                'tipo' => $tipo,
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'motivo' => $motivo,
                'referencia_tipo' => $contexto['referencia_tipo'] ?? null,
                'referencia_id' => $contexto['referencia_id'] ?? null,
                'orden_servicio_id' => $contexto['orden_servicio_id'] ?? null,
                'reparacion_id' => $contexto['reparacion_id'] ?? null,
                'fecha' => now(),
                'observaciones' => $contexto['observaciones'] ?? null,
                'estado' => 'ACTIVO',
                'usuario_creador_id' => Auth::id(),
            ]);

            $producto->update([
                'cantidad' => $stockNuevo,
                'usuario_modificador_id' => Auth::id(),
            ]);

            return $movimiento;
        });
    }
}