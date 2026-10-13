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
        Schema::table('orden_fotos', function (Blueprint $table) {
            $table->foreignId('orden_diagnostico_detalle_id')
                ->nullable()
                ->after('orden_inspeccion_id')
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
