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
        // @tabla movimientos_inventario: Entradas y salidas de productos con saldos de existencias.
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->comment('Entradas y salidas de productos con saldos de existencias.');
            $table->id()->comment('Identificador unico de la tabla movimientos_inventario.');
            $table->foreignId('producto_id')
                ->comment('Clave foranea que referencia a productos.id. Producto asociado al registro.')
                ->constrained('productos')
                ->restrictOnDelete();

            $table->string('tipo', 30)->comment('Clasificacion del registro dentro de su modulo (tabla movimientos_inventario).');
            $table->decimal('cantidad', 12, 2)->comment('Cantidad registrada en la unidad de medida correspondiente (tabla movimientos_inventario).');
            $table->decimal('stock_anterior', 12, 2)->comment('Existencias del producto antes del movimiento (tabla movimientos_inventario).');
            $table->decimal('stock_nuevo', 12, 2)->comment('Existencias del producto despues del movimiento (tabla movimientos_inventario).');
            $table->string('motivo', 50)->comment('Motivo del movimiento de inventario (tabla movimientos_inventario).');
            $table->string('referencia_tipo', 50)->nullable()->comment('Tipo de documento o entidad de origen del movimiento (tabla movimientos_inventario).');
            $table->unsignedBigInteger('referencia_id')->nullable()->comment('Identificador de la referencia de origen; no declara una clave foranea (tabla movimientos_inventario).');
            $table->foreignId('orden_servicio_id')
                ->nullable()
                ->comment('Clave foranea que referencia a orden_servicios.id. Orden de servicio a la que pertenece el registro.')
                ->constrained('orden_servicios')
                ->nullOnDelete();
            $table->foreignId('reparacion_id')
                ->nullable()
                ->comment('Clave foranea que referencia a reparaciones.id. Reparacion asociada al registro.')
                ->constrained('reparaciones')
                ->nullOnDelete();
            $table->dateTime('fecha')->useCurrent()->comment('Fecha y hora del evento registrado (tabla movimientos_inventario).');
            $table->text('observaciones')->nullable()->comment('Notas adicionales del registro (tabla movimientos_inventario).');
            $table->string('estado', 30)
                ->default('ACTIVO')->comment('Estado del registro dentro de su proceso o catalogo (tabla movimientos_inventario).');
            $table->foreignId('usuario_creador_id')
                ->nullable()
                ->comment('Clave foranea que referencia a users.id. Usuario que creo el registro.')
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('usuario_modificador_id')
                ->nullable()
                ->comment('Clave foranea que referencia a users.id. Usuario de la ultima modificacion registrada.')
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('usuario_eliminador_id')
                ->nullable()
                ->comment('Clave foranea que referencia a users.id. Usuario que registro la eliminacion.')
                ->constrained('users')
                ->nullOnDelete();
            $table->softDeletes()->comment('fecha y hora de eliminacion logica; nulo indica que no se ha eliminado (tabla movimientos_inventario).');
            $table->timestamp('created_at')->nullable()->comment('Fecha y hora de creacion del registro de la tabla movimientos_inventario.');
            $table->timestamp('updated_at')->nullable()->comment('Fecha y hora de ultima actualizacion del registro de la tabla movimientos_inventario.');
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
