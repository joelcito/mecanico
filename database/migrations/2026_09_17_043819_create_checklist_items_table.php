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
        Schema::create('checklist_items', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 150);

            $table->enum('tipo_vehiculo', [
                'AUTOMOVIL',
                'MOTOCICLETA',
            ]);

            $table->enum('tipo_propulsion', [
                'COMBUSTION',
                'ELECTRICO',
                'HIBRIDO',
            ])->nullable();

            $table->unsignedInteger('orden')->default(0);

            $table->string('estado', 30)->default('ACTIVO');

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

            $table->index([
                'tipo_vehiculo',
                'tipo_propulsion',
                'estado',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_items');
    }
};
