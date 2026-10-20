<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // @tabla orden_fotos: Evidencias fotograficas de las ordenes de servicio.
        Schema::table('orden_fotos', function (Blueprint $table) {
            $table->foreignId('orden_diagnostico_detalle_id')
                ->nullable()
                ->after('orden_inspeccion_id')
                ->comment('Clave foranea que referencia a orden_diagnostico_detalles.id. Hallazgo del diagnostico asociado a la fotografia.')
                ->constrained('orden_diagnostico_detalles')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orden_fotos', function (Blueprint $table) {
            $table->dropConstrainedForeignId(
                'orden_diagnostico_detalle_id'
            );
        });
    }
};
