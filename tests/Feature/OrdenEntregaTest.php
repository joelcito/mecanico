<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OrdenEntregaTest extends TestCase
{
    use RefreshDatabase;

    private User $actor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actor = User::factory()->create();
        $this->actingAs($this->actor);
    }

    public function test_verification_uses_only_active_payments_and_delivery_is_blocked_by_debt(): void
    {
        $ordenId = $this->crearOrdenLista(60, [
            ['monto' => 20, 'estado' => 'ACTIVO'],
            ['monto' => 40, 'estado' => 'ANULADO'],
        ]);

        $this->getJson(route('ordenServicio.verificarDeuda', $ordenId))
            ->assertOk()
            ->assertJsonPath('data.total', 60)
            ->assertJsonPath('data.pagado', 20)
            ->assertJsonPath('data.saldo', 40)
            ->assertJsonPath('data.tiene_deuda', true)
            ->assertJsonPath('data.puede_entregar', false);

        $this->putJson(route('ordenServicio.entregar', $ordenId))
            ->assertUnprocessable()
            ->assertJsonPath('errors.saldo.0', 'No se puede realizar la entrega mientras exista un saldo pendiente.');

        $this->assertDatabaseHas('orden_servicios', ['id' => $ordenId, 'estado' => 'LISTO']);
        $this->assertDatabaseMissing('entregas', ['orden_servicio_id' => $ordenId]);
    }

    public function test_delivery_requires_a_finalized_repair(): void
    {
        $ordenId = $this->crearOrdenLista(60, [], 'EN_PROCESO');

        $this->getJson(route('ordenServicio.verificarDeuda', $ordenId))
            ->assertOk()
            ->assertJsonPath('data.puede_entregar', false)
            ->assertJsonPath('data.motivo', 'La reparación de esta orden todavía no ha finalizado.');

        $this->putJson(route('ordenServicio.entregar', $ordenId))
            ->assertUnprocessable()
            ->assertJsonPath('errors.reparacion.0', 'La reparación de esta orden todavía no ha finalizado.');
    }

    public function test_fully_paid_order_can_be_delivered_only_once_and_has_a_pdf_receipt(): void
    {
        $ordenId = $this->crearOrdenLista(60, [
            ['monto' => 60, 'estado' => 'ACTIVO'],
        ]);

        $this->getJson(route('ordenServicio.verificarDeuda', $ordenId))
            ->assertOk()
            ->assertJsonPath('data.saldo', 0)
            ->assertJsonPath('data.tiene_deuda', false)
            ->assertJsonPath('data.puede_entregar', true);

        $this->putJson(route('ordenServicio.entregar', $ordenId))
            ->assertOk()
            ->assertJsonPath('estado', true)
            ->assertJsonPath('data.entrega.orden_servicio_id', $ordenId);

        $this->assertDatabaseHas('orden_servicios', ['id' => $ordenId, 'estado' => 'ENTREGADO']);
        $this->assertDatabaseCount('entregas', 1);

        $this->putJson(route('ordenServicio.entregar', $ordenId))
            ->assertUnprocessable();

        $this->get(route('ordenServicio.entrega.boleta', $ordenId))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    private function crearOrdenLista(float $total, array $pagos, string $estadoReparacion = 'FINALIZADA'): int
    {
        $sucursalId = DB::table('sucursales')->insertGetId([
            'nombre' => 'Sucursal de prueba',
            'estado' => 'ACTIVO',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $clienteId = DB::table('clientes')->insertGetId([
            'user_id' => $this->actor->id,
            'estado' => 'ACTIVO',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $marcaId = DB::table('marcas')->insertGetId([
            'nombre' => 'Nissan',
            'tipo' => 'AUTO',
            'estado' => 'ACTIVO',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $vehiculoId = DB::table('vehiculos')->insertGetId([
            'cliente_id' => $clienteId,
            'marca_id' => $marcaId,
            'modelo' => '78',
            'placa' => 'TEST-' . uniqid(),
            'tipo_vehiculo' => 'AUTOMOVIL',
            'estado' => 'ACTIVO',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $ordenId = DB::table('orden_servicios')->insertGetId([
            'numero_orden' => 'OR-' . uniqid(),
            'vehiculo_id' => $vehiculoId,
            'fecha_recepcion' => now(),
            'estado' => 'LISTO',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('orden_cotizaciones')->insert([
            'orden_servicio_id' => $ordenId,
            'fecha' => now(),
            'subtotal' => $total,
            'descuento' => 0,
            'total' => $total,
            'estado' => 'APROBADA',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('reparaciones')->insert([
            'orden_servicio_id' => $ordenId,
            'tecnico_id' => $this->actor->id,
            'fecha_inicio' => now(),
            'fecha_fin' => $estadoReparacion === 'FINALIZADA' ? now() : null,
            'trabajos_realizados' => 'Servicio realizado',
            'estado' => $estadoReparacion,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($pagos as $pago) {
            DB::table('pagos')->insert([
                'orden_servicio_id' => $ordenId,
                'sucursal_id' => $sucursalId,
                'monto' => $pago['monto'],
                'cambio' => 0,
                'monto_recibido' => $pago['monto'],
                'fecha' => now(),
                'tipo_pago' => 'EFECTIVO',
                'estado' => $pago['estado'],
                'usuario_creador_id' => $this->actor->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $ordenId;
    }
}