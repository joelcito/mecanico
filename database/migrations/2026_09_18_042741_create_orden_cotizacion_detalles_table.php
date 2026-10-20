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
        // @tabla orden_cotizacion_detalles: Conceptos de productos o servicios cotizados.
        Schema::create('orden_cotizacion_detalles', function (Blueprint $table) {
            $table->comment('Conceptos de productos o servicios cotizados.');
            $table->id()->comment('Identificador unico de la tabla orden_cotizacion_detalles.');

            $table->foreignId('orden_cotizacion_id')
                ->comment('Clave foranea que referencia a orden_cotizaciones.id. Cotizacion a la que pertenece el detalle.')
                ->constrained('orden_cotizaciones')
                ->cascadeOnDelete();

            $table->string('tipo', 30)
                ->default('PRODUCTO')->comment('Clasificacion del registro dentro de su modulo (tabla orden_cotizacion_detalles).');

            $table->foreignId('producto_id')
                ->nullable()
                ->comment('Clave foranea que referencia a productos.id. Producto asociado al registro.')
                ->constrained('productos')
                ->nullOnDelete();

            $table->string('descripcion', 255)->comment('Descripcion del registro o concepto (tabla orden_cotizacion_detalles).');
            $table->decimal('cantidad', 12, 2)->default(1)->comment('Cantidad registrada en la unidad de medida correspondiente (tabla orden_cotizacion_detalles).');
            $table->decimal('precio_unitario', 12, 2)->default(0)->comment('Precio por unidad del concepto o producto (tabla orden_cotizacion_detalles).');

            $table->decimal('subtotal', 12, 2)->default(0)->comment('Importe parcial registrado antes de los ajustes del total (tabla orden_cotizacion_detalles).');

            $table->string('estado', 30)->default('ACTIVO')->comment('Estado del registro dentro de su proceso o catalogo (tabla orden_cotizacion_detalles).');

            // Auditoría
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

            $table->softDeletes()->comment('fecha y hora de eliminacion logica; nulo indica que no se ha eliminado (tabla orden_cotizacion_detalles).');
            $table->timestamp('created_at')->nullable()->comment('Fecha y hora de creacion del registro de la tabla orden_cotizacion_detalles.');
            $table->timestamp('updated_at')->nullable()->comment('Fecha y hora de ultima actualizacion del registro de la tabla orden_cotizacion_detalles.');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_cotizacion_detalles');
    }
};
