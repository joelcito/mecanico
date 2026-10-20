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
        // @tabla orden_diagnosticos: Diagnosticos tecnicos de las ordenes de servicio.
        Schema::create('orden_diagnosticos', function (Blueprint $table) {
            $table->comment('Diagnosticos tecnicos de las ordenes de servicio.');
            $table->id()->comment('Identificador unico de la tabla orden_diagnosticos.');
            $table->foreignId('orden_servicio_id')
                ->comment('Clave foranea que referencia a orden_servicios.id. Orden de servicio a la que pertenece el registro.')
                ->constrained('orden_servicios')
                ->restrictOnDelete();

            $table->foreignId('usuario_diagnostico_id')
                ->nullable()
                ->comment('Clave foranea que referencia a users.id. Usuario responsable del diagnostico.')
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('fecha')->nullable()->comment('Fecha y hora del evento registrado (tabla orden_diagnosticos).');
            $table->text('descripcion')->nullable()->comment('Descripcion del registro o concepto (tabla orden_diagnosticos).');
            $table->text('observaciones')->nullable()->comment('Notas adicionales del registro (tabla orden_diagnosticos).');
            $table->string('estado', 30)
                ->default('FINALIZADO')->comment('Estado del registro dentro de su proceso o catalogo (tabla orden_diagnosticos).');

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
            $table->softDeletes()->comment('fecha y hora de eliminacion logica; nulo indica que no se ha eliminado (tabla orden_diagnosticos).');
            $table->timestamp('created_at')->nullable()->comment('Fecha y hora de creacion del registro de la tabla orden_diagnosticos.');
            $table->timestamp('updated_at')->nullable()->comment('Fecha y hora de ultima actualizacion del registro de la tabla orden_diagnosticos.');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_diagnosticos');
    }
};
