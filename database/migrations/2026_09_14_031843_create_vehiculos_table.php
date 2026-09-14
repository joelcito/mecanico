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
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->foreignId('marca_id')->constrained('marcas')->restrictOnDelete();
            $table->string('modelo', 100);
            $table->year('anio')->nullable();
            $table->string('placa', 20)->unique();
            $table->string('color', 50)->nullable();

            $table->enum('tipo_vehiculo', [
                'AUTOMOVIL',
                'MOTOCICLETA'
            ])->default('AUTOMOVIL');

            $table->string('vin', 50)->nullable()->unique();
            $table->string('numero_motor', 50)->nullable();
            $table->text('observaciones')->nullable();
            $table->string('estado')->nullable();
            $table->foreignId('usuario_creador_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('usuario_modificador_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('usuario_eliminador_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
