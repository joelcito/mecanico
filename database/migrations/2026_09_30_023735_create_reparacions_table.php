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
        Schema::create('reparaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('orden_servicio_id')
                ->constrained('orden_servicios')
                ->restrictOnDelete();

            $table->foreignId('tecnico_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('fecha_inicio')->nullable();
            $table->dateTime('fecha_fin')->nullable();

            $table->text('trabajos_realizados')->nullable();
            $table->text('observaciones')->nullable();

            $table->string('estado', 30)->default('EN_PROCESO');

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
        Schema::dropIfExists('reparacions');
    }
};
