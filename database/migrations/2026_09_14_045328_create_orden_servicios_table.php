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
        // @tabla orden_servicios: Ordenes de servicio y datos de recepcion del vehiculo.
        Schema::create('orden_servicios', function (Blueprint $table) {
            $table->comment('Ordenes de servicio y datos de recepcion del vehiculo.');
            $table->id()->comment('Identificador unico de la tabla orden_servicios.');
            $table->string('numero_orden', 20)->unique()->comment('Numero unico de la orden de servicio (tabla orden_servicios).');

            $table->foreignId('vehiculo_id')
                ->comment('Clave foranea que referencia a vehiculos.id. Vehiculo de la orden de servicio.')
                ->constrained('vehiculos')
                ->restrictOnDelete();


            $table->dateTime('fecha_recepcion')->comment('Fecha y hora de recepcion del vehiculo (tabla orden_servicios).');
            $table->unsignedInteger('kilometraje')->nullable()->comment('Kilometraje registrado al recibir el vehiculo (tabla orden_servicios).');
            $table->string('motivo_ingreso', 255)->nullable()->comment('Motivo por el que ingresa el vehiculo al taller (tabla orden_servicios).');
            $table->decimal('nivel_combustible', 5, 2)->nullable()->comment('Porcentaje de combustible registrado en la recepcion (0 a 100) (tabla orden_servicios).');
            $table->text('observaciones')->nullable()->comment('Notas adicionales del registro (tabla orden_servicios).');
            $table->string('estado', 50)->default('RECIBIDO')->comment('Estado del registro dentro de su proceso o catalogo (tabla orden_servicios).');
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

            $table->timestamp('created_at')->nullable()->comment('Fecha y hora de creacion del registro de la tabla orden_servicios.');
            $table->timestamp('updated_at')->nullable()->comment('Fecha y hora de ultima actualizacion del registro de la tabla orden_servicios.');
            $table->softDeletes()->comment('fecha y hora de eliminacion logica; nulo indica que no se ha eliminado (tabla orden_servicios).');
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
