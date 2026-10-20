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
        // @tabla orden_diagnostico_detalles: Hallazgos y recomendaciones de un diagnostico.
        Schema::create('orden_diagnostico_detalles', function (Blueprint $table) {
            $table->comment('Hallazgos y recomendaciones de un diagnostico.');

            $table->id()->comment('Identificador unico de la tabla orden_diagnostico_detalles.');

            $table->foreignId('orden_diagnostico_id')
                ->comment('Clave foranea que referencia a orden_diagnosticos.id. Diagnostico al que pertenece el detalle.')
                ->constrained('orden_diagnosticos')
                ->cascadeOnDelete();

            $table->text('descripcion')->comment('Descripcion del registro o concepto (tabla orden_diagnostico_detalles).');

            $table->string('tipo', 30)
                ->default('HALLAZGO')->comment('Clasificacion del registro dentro de su modulo (tabla orden_diagnostico_detalles).');

            $table->string('prioridad', 20)
                ->default('MEDIA')->comment('Prioridad asignada al hallazgo o recomendacion (tabla orden_diagnostico_detalles).');

            $table->text('observacion')->nullable()->comment('Nota adicional del detalle (tabla orden_diagnostico_detalles).');

            $table->string('estado', 30)
                ->default('ACTIVO')->comment('Estado del registro dentro de su proceso o catalogo (tabla orden_diagnostico_detalles).');

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

            $table->softDeletes()->comment('fecha y hora de eliminacion logica; nulo indica que no se ha eliminado (tabla orden_diagnostico_detalles).');

            $table->timestamp('created_at')->nullable()->comment('Fecha y hora de creacion del registro de la tabla orden_diagnostico_detalles.');
            $table->timestamp('updated_at')->nullable()->comment('Fecha y hora de ultima actualizacion del registro de la tabla orden_diagnostico_detalles.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_diagnostico_detalles');
    }
};
