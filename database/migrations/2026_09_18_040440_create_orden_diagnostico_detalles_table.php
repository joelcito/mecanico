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
        Schema::create('orden_diagnostico_detalles', function (Blueprint $table) {

            $table->id();

            $table->foreignId('orden_diagnostico_id')
                ->constrained('orden_diagnosticos')
                ->cascadeOnDelete();

            $table->text('descripcion');

            $table->string('tipo', 30)
                ->default('HALLAZGO');

            $table->string('prioridad', 20)
                ->default('MEDIA');

            $table->text('observacion')->nullable();

            $table->string('estado', 30)
                ->default('ACTIVO');

            // Auditoría
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
        Schema::dropIfExists('orden_diagnostico_detalles');
    }
};
