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
        Schema::create('reparacion_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reparacion_id')
                ->constrained('reparaciones')
                ->cascadeOnDelete();
            $table->foreignId('producto_id')
                ->constrained('productos')
                ->restrictOnDelete();

            $table->decimal('cantidad', 12, 2);
            $table->decimal('precio_unitario', 12, 2)
                ->default(0);
            $table->decimal('subtotal', 12, 2)
                ->default(0);
            $table->string('tipo_uso', 30)
                ->default('REPUESTO');
            $table->string('origen', 30)
                ->default('TALLER');
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
        Schema::dropIfExists('reparacion_detalles');
    }
};
