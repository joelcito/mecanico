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
        Schema::table('orden_cotizaciones', function (Blueprint $table) {
            $table->dateTime('fecha_respuesta')
                ->nullable()
                ->after('estado');

            $table->foreignId('usuario_respuesta_id')
                ->nullable()
                ->after('fecha_respuesta')
                ->constrained('users')
                ->nullOnDelete();

            $table->text('observacion_respuesta')
                ->nullable()
                ->after('usuario_respuesta_id');
        });
    }

    /**
     * Reverse the migrations.
     */
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
