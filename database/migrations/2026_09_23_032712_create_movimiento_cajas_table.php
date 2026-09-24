<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_caja', function (Blueprint $table) {
            $table->id();

            $table->foreignId('caja_id')
                ->constrained('cajas')
                ->restrictOnDelete();

            $table->foreignId('pago_id')
                ->nullable()
                ->constrained('pagos')
                ->nullOnDelete();

            $table->string('tipo', 30);

            $table->string('metodo_pago', 30)->nullable();

            $table->decimal('monto', 12, 2);

            $table->string('origen_dinero', 50)->nullable();

            $table->text('descripcion')->nullable();

            $table->dateTime('fecha');

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
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_caja');
    }
};