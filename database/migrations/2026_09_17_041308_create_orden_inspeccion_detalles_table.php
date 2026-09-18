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
        Schema::create('orden_inspeccion_detalles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('orden_inspeccion_id')
                ->constrained('orden_inspecciones')
                ->cascadeOnDelete();

            $table->string('item', 150);
            $table->string('resultado', 20)->default('NA');
            $table->text('observacion')->nullable();
            $table->string('estado', 30)->default('ACTIVO');
            $table->foreignId('usuario_creador_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('usuario_modificador_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('usuario_eliminador_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_inspeccion_detalles');
    }
};
