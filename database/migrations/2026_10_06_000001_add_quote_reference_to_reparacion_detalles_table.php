<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reparacion_detalles', function (Blueprint $table) {
            $table->foreignId('orden_cotizacion_detalle_id')
                ->nullable()
                ->after('producto_id')
                ->constrained('orden_cotizacion_detalles')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reparacion_detalles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('orden_cotizacion_detalle_id');
        });
    }
};