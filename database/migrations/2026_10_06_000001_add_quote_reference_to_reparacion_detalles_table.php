<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // @tabla reparacion_detalles: Productos utilizados en las reparaciones.
        Schema::table('reparacion_detalles', function (Blueprint $table) {
            $table->foreignId('orden_cotizacion_detalle_id')
                ->nullable()
                ->after('producto_id')
                ->comment('Clave foranea que referencia a orden_cotizacion_detalles.id. Detalle cotizado que da origen al consumo en la reparacion.')
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