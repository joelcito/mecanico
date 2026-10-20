<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // @tabla asignaciones_herramientas: Asignaciones y devoluciones de herramientas a mecanicos.
        Schema::table('asignaciones_herramientas', function (Blueprint $table) {
            $table->foreignId('reparacion_id')
                ->nullable()
                ->after('herramienta_id')
                ->comment('Clave foranea que referencia a reparaciones.id. Reparacion asociada al registro.')
                ->constrained('reparaciones')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('asignaciones_herramientas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reparacion_id');
        });
    }
};