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
        // @tabla orden_cotizaciones: Cotizaciones de las ordenes de servicio y sus importes.
        Schema::create('orden_cotizaciones', function (Blueprint $table) {
            $table->comment('Cotizaciones de las ordenes de servicio y sus importes.');
            $table->id()->comment('Identificador unico de la tabla orden_cotizaciones.');

            $table->foreignId('orden_servicio_id')
                ->comment('Clave foranea que referencia a orden_servicios.id. Orden de servicio a la que pertenece el registro.')
                ->constrained('orden_servicios')
                ->restrictOnDelete();

            $table->foreignId('usuario_cotizador_id')
                ->nullable()
                ->comment('Clave foranea que referencia a users.id. Usuario que elabora la cotizacion.')
                ->constrained('users')
                ->nullOnDelete();
            $table->dateTime('fecha')->nullable()->comment('Fecha y hora del evento registrado (tabla orden_cotizaciones).');
            $table->decimal('subtotal', 12, 2)->default(0)->comment('Importe parcial registrado antes de los ajustes del total (tabla orden_cotizaciones).');
            $table->decimal('descuento', 12, 2)->default(0)->comment('Importe del descuento aplicado a la cotizacion (tabla orden_cotizaciones).');
            $table->decimal('total', 12, 2)->default(0)->comment('Importe total de la cotizacion (tabla orden_cotizaciones).');
            $table->string('estado', 30)->default('PENDIENTE')->comment('Estado del registro dentro de su proceso o catalogo (tabla orden_cotizaciones).');
            $table->text('observaciones')->nullable()->comment('Notas adicionales del registro (tabla orden_cotizaciones).');
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
            $table->softDeletes()->comment('fecha y hora de eliminacion logica; nulo indica que no se ha eliminado (tabla orden_cotizaciones).');
            $table->timestamp('created_at')->nullable()->comment('Fecha y hora de creacion del registro de la tabla orden_cotizaciones.');
            $table->timestamp('updated_at')->nullable()->comment('Fecha y hora de ultima actualizacion del registro de la tabla orden_cotizaciones.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::dropIfExists('orden_cotizaciones');
}
};
