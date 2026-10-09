<?php

namespace Tests\Feature;

use App\Models\Herramienta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AsignacionHerramientaTest extends TestCase
{
    use RefreshDatabase;

    private User $administrador;
    private User $tecnico;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('roles')->insert([
            ['id' => 1, 'nombre' => 'Administrador', 'estado' => 'ACTIVO', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'Tecnico', 'estado' => 'ACTIVO', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nombre' => 'Encargado', 'estado' => 'ACTIVO', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->administrador = User::factory()->create(['rol_id' => 1]);
        $this->tecnico = User::factory()->create(['rol_id' => 2]);
        $this->actingAs($this->administrador);
    }

    public function test_admin_assigns_tool_to_a_technician_without_repair_dependency(): void
    {
        $herramienta = $this->crearHerramienta();

        $this->postJson(route('asignacionesHerramientas.guardar'), [
            'tecnico_id' => $this->tecnico->id,
            'herramienta_id' => $herramienta->id,
            'fecha_asignacion' => now()->format('Y-m-d H:i:s'),
            'observaciones' => 'Entrega de turno',
        ])->assertCreated()
            ->assertJsonPath('data.mecanico_id', $this->tecnico->id)
            ->assertJsonPath('data.reparacion_id', null)
            ->assertJsonPath('data.estado', 'ASIGNADA');

        $this->assertDatabaseHas('herramientas', [
            'id' => $herramienta->id,
            'estado_disponibilidad' => 'ASIGNADA',
        ]);
        $this->assertDatabaseHas('asignaciones_herramientas', [
            'herramienta_id' => $herramienta->id,
            'mecanico_id' => $this->tecnico->id,
            'reparacion_id' => null,
            'estado' => 'ASIGNADA',
        ]);
    }

    public function test_a_tool_with_an_active_assignment_cannot_be_assigned_again(): void
    {
        $herramienta = $this->crearHerramienta();
        $this->asignar($herramienta, $this->tecnico);
        $otroTecnico = User::factory()->create(['rol_id' => 2]);

        $this->postJson(route('asignacionesHerramientas.guardar'), [
            'tecnico_id' => $otroTecnico->id,
            'herramienta_id' => $herramienta->id,
            'fecha_asignacion' => now()->format('Y-m-d H:i:s'),
        ])->assertUnprocessable()
            ->assertJsonPath('errors.herramienta_id.0', 'La herramienta ya se encuentra asignada a otro técnico.');

        $this->assertDatabaseCount('asignaciones_herramientas', 1);
    }

    public function test_return_records_receiver_and_makes_tool_available_for_reassignment(): void
    {
        $herramienta = $this->crearHerramienta();
        $asignacionId = $this->asignar($herramienta, $this->tecnico)->json('data.id');

        $this->putJson(route('asignacionesHerramientas.devolver', $asignacionId), [
            'observaciones_devolucion' => 'Herramienta completa',
        ])->assertOk()
            ->assertJsonPath('data.estado', 'DEVUELTA')
            ->assertJsonPath('data.usuario_devolucion_id', $this->administrador->id);

        $this->assertDatabaseHas('herramientas', [
            'id' => $herramienta->id,
            'estado_disponibilidad' => 'DISPONIBLE',
        ]);

        $otroTecnico = User::factory()->create(['rol_id' => 2]);
        $this->postJson(route('asignacionesHerramientas.guardar'), [
            'tecnico_id' => $otroTecnico->id,
            'herramienta_id' => $herramienta->id,
            'fecha_asignacion' => now()->format('Y-m-d H:i:s'),
        ])->assertCreated();

        $this->assertDatabaseCount('asignaciones_herramientas', 2);
    }

    public function test_technician_can_only_view_own_assignments_and_cannot_manage_them(): void
    {
        $herramienta = $this->crearHerramienta();
        $this->asignar($herramienta, $this->tecnico);
        $otroTecnico = User::factory()->create(['rol_id' => 2]);
        $otraHerramienta = $this->crearHerramienta('DISPONIBLE', 'Llave de prueba');
        $this->asignar($otraHerramienta, $otroTecnico);

        $this->actingAs($this->tecnico)
            ->get(route('asignacionesHerramientas.listado'))
            ->assertOk()
            ->assertSee($herramienta->nombre)
            ->assertDontSee($otraHerramienta->nombre);

        $this->postJson(route('asignacionesHerramientas.guardar'), [
            'tecnico_id' => $this->tecnico->id,
            'herramienta_id' => $herramienta->id,
            'fecha_asignacion' => now()->format('Y-m-d H:i:s'),
        ])->assertForbidden();
    }

    private function asignar(Herramienta $herramienta, User $tecnico)
    {
        return $this->postJson(route('asignacionesHerramientas.guardar'), [
            'tecnico_id' => $tecnico->id,
            'herramienta_id' => $herramienta->id,
            'fecha_asignacion' => now()->format('Y-m-d H:i:s'),
        ])->assertCreated();
    }

    private function crearHerramienta(string $estadoDisponibilidad = 'DISPONIBLE', string $nombre = 'Taladro de prueba'): Herramienta
    {
        return Herramienta::create([
            'codigo' => 'HER-' . uniqid(),
            'nombre' => $nombre,
            'unidad_medida' => 'UNIDAD',
            'cantidad' => 1,
            'stock_minimo' => 0,
            'estado' => 'ACTIVO',
            'estado_disponibilidad' => $estadoDisponibilidad,
        ]);
    }
}