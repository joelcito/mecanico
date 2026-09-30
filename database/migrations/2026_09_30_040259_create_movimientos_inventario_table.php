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
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')
                ->constrained('productos')
                ->restrictOnDelete();

            $table->string('tipo', 30);
            $table->decimal('cantidad', 12, 2);
            $table->decimal('stock_anterior', 12, 2);
            $table->decimal('stock_nuevo', 12, 2);
            $table->string('motivo', 50);
            $table->string('referencia_tipo', 50)->nullable();
            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->foreignId('orden_servicio_id')
                ->nullable()
                ->constrained('orden_servicios')
                ->nullOnDelete();
            $table->foreignId('reparacion_id')
                ->nullable()
                ->constrained('reparaciones')
                ->nullOnDelete();
            $table->dateTime('fecha')->useCurrent();
            $table->text('observaciones')->nullable();
            $table->string('estado', 30)
                ->default('ACTIVO');
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
        Schema::dropIfExists('movimientos_inventario');
    }
};
