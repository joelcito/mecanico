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
        // @tabla reparaciones: Ejecucion de reparaciones y trabajos realizados.
        Schema::create('reparaciones', function (Blueprint $table) {
            $table->comment('Ejecucion de reparaciones y trabajos realizados.');
            $table->id()->comment('Identificador unico de la tabla reparaciones.');

            $table->foreignId('orden_servicio_id')
                ->comment('Clave foranea que referencia a orden_servicios.id. Orden de servicio a la que pertenece el registro.')
                ->constrained('orden_servicios')
                ->restrictOnDelete();

            $table->foreignId('tecnico_id')
                ->nullable()
                ->comment('Clave foranea que referencia a users.id. Usuario tecnico asociado al registro.')
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('fecha_inicio')->nullable()->comment('Fecha y hora de inicio de la reparacion (tabla reparaciones).');
            $table->dateTime('fecha_fin')->nullable()->comment('Fecha y hora de finalizacion de la reparacion (tabla reparaciones).');

            $table->text('trabajos_realizados')->nullable()->comment('Descripcion de los trabajos ejecutados en la reparacion (tabla reparaciones).');
            $table->text('observaciones')->nullable()->comment('Notas adicionales del registro (tabla reparaciones).');

            $table->string('estado', 30)->default('EN_PROCESO')->comment('Estado del registro dentro de su proceso o catalogo (tabla reparaciones).');

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

            $table->softDeletes()->comment('fecha y hora de eliminacion logica; nulo indica que no se ha eliminado (tabla reparaciones).');
            $table->timestamp('created_at')->nullable()->comment('Fecha y hora de creacion del registro de la tabla reparaciones.');
            $table->timestamp('updated_at')->nullable()->comment('Fecha y hora de ultima actualizacion del registro de la tabla reparaciones.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reparacions');
    }
};
