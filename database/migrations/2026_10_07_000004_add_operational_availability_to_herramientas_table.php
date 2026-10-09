<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('herramientas', function (Blueprint $table) {
            $table->string('estado_disponibilidad', 30)
                ->default('DISPONIBLE')
                ->after('estado');
        });

        Schema::table('asignaciones_herramientas', function (Blueprint $table) {
            $table->foreignId('usuario_devolucion_id')
                ->nullable()
                ->after('usuario_modificador_id')
                ->constrained('users')
                ->nullOnDelete();
        });

        $herramientasAsignadas = DB::table('asignaciones_herramientas')
            ->whereNull('deleted_at')
            ->whereIn('estado', ['ASIGNADA', 'ACTIVA'])
            ->whereNotNull('herramienta_id')
            ->distinct()
            ->pluck('herramienta_id');

        if ($herramientasAsignadas->isNotEmpty()) {
            DB::table('herramientas')
                ->whereIn('id', $herramientasAsignadas)
                ->update(['estado_disponibilidad' => 'ASIGNADA']);
        }
    }

    public function down(): void
    {
        Schema::table('asignaciones_herramientas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('usuario_devolucion_id');
        });

        Schema::table('herramientas', function (Blueprint $table) {
            $table->dropColumn('estado_disponibilidad');
        });
    }
};