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
        Schema::create('orden_servicios', function (Blueprint $table) {
            $table->id();
            $table->string('numero_orden', 20)->unique();

            $table->foreignId('vehiculo_id')
                ->constrained('vehiculos')
                ->restrictOnDelete();


            $table->dateTime('fecha_recepcion');
            $table->unsignedInteger('kilometraje')->nullable();
            $table->string('motivo_ingreso', 255)->nullable();
            $table->decimal('nivel_combustible', 5, 2)->nullable();
            $table->text('observaciones')->nullable();
            $table->string('estado', 50)->default('RECIBIDO');
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

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_servicios');
    }
};
