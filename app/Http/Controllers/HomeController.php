<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Models\OrdenServicio;
use App\Models\Reparacion;
use App\Models\Producto;
use App\Models\Pago;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today();
        $inicioMes = $hoy->copy()->startOfMonth();
        $finMes = $hoy->copy()->endOfMonth();

        // 1. MÉTRICAS GENERALES
        $totalVehiculos = Vehiculo::count();

        $totalClientes = Cliente::count();

        // Órdenes que tienen una reparación todavía activa.
        $vehiculosEnReparacion = OrdenServicio::whereHas(
            'reparacion',
            function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('estado')
                        ->orWhereNotIn('estado', [
                            'FINALIZADA',
                            'CANCELADA',
                            'ANULADA',
                        ]);
                });
            }
        )
        ->whereNotIn('estado', ['CANCELADA', 'ANULADA'])
        ->count();

        // 2. INGRESOS REALES: PAGOS ACTIVOS
        $ingresosHoy = Pago::where('estado', 'ACTIVO')
            ->whereDate('fecha', $hoy)
            ->sum('monto');

        $ingresosMes = Pago::where('estado', 'ACTIVO')
            ->whereBetween('fecha', [
                $inicioMes,
                $finMes,
            ])
            ->sum('monto');

        // 3. TRABAJOS ACTIVOS DEL TALLER
        $trabajosEnTaller = OrdenServicio::with([
            'vehiculo.marca',
            'vehiculo.cliente.user',
            'reparacion',
        ])
        ->whereHas('reparacion', function ($query) {
            $query->where(function ($q) {
                $q->whereNull('estado')
                    ->orWhereNotIn('estado', [
                        'FINALIZADA',
                        'CANCELADA',
                        'ANULADA',
                    ]);
            });
        })
        ->whereNotIn('estado', ['CANCELADA', 'ANULADA'])
        ->latest('fecha_recepcion')
        ->take(6)
        ->get()
        ->map(function ($orden) {
            $vehiculo = $orden->vehiculo;
            $cliente = $vehiculo?->cliente;

            return (object) [
                'id' => $orden->id,
                'vehiculo' => trim(
                    ($vehiculo?->marca?->nombre ?? '') . ' ' .
                    ($vehiculo?->modelo ?? '') . ' ' .
                    ($vehiculo?->placa ?? '')
                ),
                'cliente' => $cliente?->razon_social
                    ?? $cliente?->user?->name
                    ?? 'Sin cliente',
                'servicio' => $orden->motivo_ingreso
                    ?? 'No especificado',
                'estado' => $orden->reparacion?->estado
                    ?? $orden->estado
                    ?? 'Pendiente',
            ];
        });

        // 4. PRODUCTOS CON STOCK BAJO
        $repuestosStockBajo = Producto::whereColumn(
            'cantidad',
            '<=',
            'stock_minimo'
        )
        ->whereNull('deleted_at')
        ->orderBy('cantidad')
        ->take(8)
        ->get();

        // 5. SERVICIOS FINALIZADOS HOY
        $serviciosHoy = Reparacion::where(
            'estado',
            'FINALIZADA'
        )
        ->whereDate('fecha_fin', $hoy)
        ->count();

        // 6. INGRESOS ASOCIADOS A ÓRDENES CON REPARACIÓN
        $ingresosServicios = Pago::where('estado', 'ACTIVO')
            ->whereBetween('fecha', [
                $inicioMes,
                $finMes,
            ])
            ->whereHas('ordenServicio.reparacion')
            ->sum('monto');

        // 7. ÚLTIMAS ÓRDENES
        $ultimasOrdenes = OrdenServicio::with([
            'vehiculo.marca',
            'vehiculo.cliente.user',
            'cotizacionActual',
        ])
        ->latest('fecha_recepcion')
        ->take(10)
        ->get()
        ->map(function ($orden) {
            $vehiculo = $orden->vehiculo;
            $cliente = $vehiculo?->cliente;

            return (object) [
                'id' => $orden->numero_orden
                    ?? $orden->id,
                'cliente' => $cliente?->razon_social
                    ?? $cliente?->user?->name
                    ?? 'Sin cliente',
                'vehiculo' => trim(
                    ($vehiculo?->marca?->nombre ?? '') . ' ' .
                    ($vehiculo?->modelo ?? '') . ' ' .
                    ($vehiculo?->placa ?? '')
                ),
                'servicio' => $orden->motivo_ingreso
                    ?? 'No especificado',
                'estado' => $orden->estado
                    ?? 'Pendiente',
                'total' => $orden->cotizacionActual?->total ?? 0,
                'fecha' => $orden->fecha_recepcion?->format('d/m/Y')
                    ?? '',
            ];
        });

        // 8. INGRESOS DE LOS ÚLTIMOS 12 MESES
        $inicioGrafico = $hoy->copy()
            ->startOfMonth()
            ->subMonths(11);

        $pagosMensuales = Pago::where('estado', 'ACTIVO')
            ->whereBetween('fecha', [
                $inicioGrafico,
                $finMes,
            ])
            ->selectRaw(
                'YEAR(fecha) as anio,
                 MONTH(fecha) as numero_mes,
                 SUM(monto) as total'
            )
            ->groupByRaw('YEAR(fecha), MONTH(fecha)')
            ->get()
            ->keyBy(function ($item) {
                return sprintf(
                    '%04d-%02d',
                    $item->anio,
                    $item->numero_mes
                );
            });

        $nombresMeses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];

        $ingresosMensuales = collect();

        for ($i = 0; $i < 12; $i++) {
            $mes = $inicioGrafico->copy()->addMonths($i);

            $clave = $mes->format('Y-m');

            $ingresosMensuales->push((object) [
                'mes' => $nombresMeses[$mes->month]
                    . ' ' . $mes->year,
                'total' => (float) (
                    $pagosMensuales->get($clave)?->total ?? 0
                ),
            ]);
        }

        // 9. CITAS
        // Pendiente de conectar cuando identifiquemos
        // el modelo y la tabla real de citas.
        $proximasCitas = collect();

        return view('home.inicio', compact(
            'totalVehiculos',
            'totalClientes',
            'vehiculosEnReparacion',
            'ingresosHoy',
            'trabajosEnTaller',
            'proximasCitas',
            'repuestosStockBajo',
            'serviciosHoy',
            'ingresosMes',
            'ingresosServicios',
            'ultimasOrdenes',
            'ingresosMensuales'
        ));
    }
}
