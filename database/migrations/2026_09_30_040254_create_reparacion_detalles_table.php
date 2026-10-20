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
        // @tabla reparacion_detalles: Productos utilizados en las reparaciones.
        Schema::create('reparacion_detalles', function (Blueprint $table) {
            $table->comment('Productos utilizados en las reparaciones.');
            $table->id()->comment('Identificador unico de la tabla reparacion_detalles.');
            $table->foreignId('reparacion_id')
                ->comment('Clave foranea que referencia a reparaciones.id. Reparacion asociada al registro.')
                ->constrained('reparaciones')
                ->cascadeOnDelete();
            $table->foreignId('producto_id')
                ->comment('Clave foranea que referencia a productos.id. Producto asociado al registro.')
                ->constrained('productos')
                ->restrictOnDelete();

            $table->decimal('cantidad', 12, 2)->comment('Cantidad registrada en la unidad de medida correspondiente (tabla reparacion_detalles).');
            $table->decimal('precio_unitario', 12, 2)
                ->default(0)->comment('Precio por unidad del concepto o producto (tabla reparacion_detalles).');
            $table->decimal('subtotal', 12, 2)
                ->default(0)->comment('Importe parcial registrado antes de los ajustes del total (tabla reparacion_detalles).');
            $table->string('tipo_uso', 30)
                ->default('REPUESTO')->comment('Clasificacion del uso del producto en la reparacion (tabla reparacion_detalles).');
            $table->string('origen', 30)
                ->default('TALLER')->comment('Procedencia del producto utilizado (tabla reparacion_detalles).');
            $table->text('observaciones')->nullable()->comment('Notas adicionales del registro (tabla reparacion_detalles).');
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
            $table->softDeletes()->comment('fecha y hora de eliminacion logica; nulo indica que no se ha eliminado (tabla reparacion_detalles).');
            $table->timestamp('created_at')->nullable()->comment('Fecha y hora de creacion del registro de la tabla reparacion_detalles.');
            $table->timestamp('updated_at')->nullable()->comment('Fecha y hora de ultima actualizacion del registro de la tabla reparacion_detalles.');
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
