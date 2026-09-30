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
        Schema::create('asignaciones_herramientas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')
                ->constrained('productos')
                ->restrictOnDelete();
            $table->foreignId('mecanico_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->decimal('cantidad', 12, 2)
                ->default(1);
            $table->dateTime('fecha_asignacion');
            $table->dateTime('fecha_devolucion')->nullable();
            $table->string('estado', 30)->default('ASIGNADA');
            $table->text('observaciones')->nullable();
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
        Schema::dropIfExists('asignaciones_herramientas');
    }
};
