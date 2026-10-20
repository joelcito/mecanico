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
        // @tabla orden_inspeccion_detalles: Resultados individuales de la inspeccion.
        Schema::create('orden_inspeccion_detalles', function (Blueprint $table) {
            $table->comment('Resultados individuales de la inspeccion.');
            $table->id()->comment('Identificador unico de la tabla orden_inspeccion_detalles.');

            $table->foreignId('orden_inspeccion_id')
                ->comment('Clave foranea que referencia a orden_inspecciones.id. Inspeccion asociada al registro.')
                ->constrained('orden_inspecciones')
                ->cascadeOnDelete();

            $table->string('item', 150)->comment('Punto o elemento evaluado en la inspeccion (tabla orden_inspeccion_detalles).');
            $table->string('resultado', 20)->default('NA')->comment('Resultado registrado para el punto de inspeccion (tabla orden_inspeccion_detalles).');
            $table->text('observacion')->nullable()->comment('Nota adicional del detalle (tabla orden_inspeccion_detalles).');
            $table->string('estado', 30)->default('ACTIVO')->comment('Estado del registro dentro de su proceso o catalogo (tabla orden_inspeccion_detalles).');
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

            $table->softDeletes()->comment('fecha y hora de eliminacion logica; nulo indica que no se ha eliminado (tabla orden_inspeccion_detalles).');
            $table->timestamp('created_at')->nullable()->comment('Fecha y hora de creacion del registro de la tabla orden_inspeccion_detalles.');
            $table->timestamp('updated_at')->nullable()->comment('Fecha y hora de ultima actualizacion del registro de la tabla orden_inspeccion_detalles.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_inspeccion_detalles');
    }
};
