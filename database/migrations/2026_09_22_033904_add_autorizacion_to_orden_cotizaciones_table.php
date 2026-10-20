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
        // @tabla orden_cotizaciones: Cotizaciones de las ordenes de servicio y sus importes.
        Schema::table('orden_cotizaciones', function (Blueprint $table) {
            $table->dateTime('fecha_respuesta')
                ->nullable()
                ->after('fecha')->comment('Fecha y hora de respuesta a la cotizacion (tabla orden_cotizaciones).');

            $table->foreignId('usuario_respuesta_id')
                ->nullable()
                ->after('usuario_cotizador_id')
                ->comment('Clave foranea que referencia a users.id. Usuario que registra la respuesta a la cotizacion.')
                ->constrained('users')
                ->nullOnDelete();

            $table->text('observacion_respuesta')
                ->nullable()
                ->after('observaciones')->comment('Observacion asociada a la respuesta de la cotizacion (tabla orden_cotizaciones).');
        });
    }

    public function down(): void
    {
        Schema::table('orden_cotizaciones', function (Blueprint $table) {
            $table->dropForeign(['usuario_respuesta_id']);
            $table->dropColumn([
                'fecha_respuesta',
                'usuario_respuesta_id',
                'observacion_respuesta',
            ]);
        });
    }
};
