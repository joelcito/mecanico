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
        // @tabla orden_fotos: Evidencias fotograficas de las ordenes de servicio.
        Schema::create('orden_fotos', function (Blueprint $table) {
            $table->comment('Evidencias fotograficas de las ordenes de servicio.');
            $table->id()->comment('Identificador unico de la tabla orden_fotos.');

            $table->foreignId('orden_servicio_id')
                ->comment('Clave foranea que referencia a orden_servicios.id. Orden de servicio a la que pertenece el registro.')
                ->constrained('orden_servicios')
                ->cascadeOnDelete();
            $table->foreignId('orden_inspeccion_id')
                ->nullable()
                ->comment('Clave foranea que referencia a orden_inspecciones.id. Inspeccion asociada al registro.')
                ->constrained('orden_inspecciones')
                ->nullOnDelete();

            $table->string('tipo', 30)->default('OTRO')->comment('Clasificacion del registro dentro de su modulo (tabla orden_fotos).');
            $table->string('ruta')->comment('Ruta del archivo de evidencia fotografica (tabla orden_fotos).');
            $table->string('descripcion', 255)->nullable()->comment('Descripcion del registro o concepto (tabla orden_fotos).');
            $table->string('estado', 30)->default('ACTIVO')->comment('Estado del registro dentro de su proceso o catalogo (tabla orden_fotos).');
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

            $table->softDeletes()->comment('fecha y hora de eliminacion logica; nulo indica que no se ha eliminado (tabla orden_fotos).');
            $table->timestamp('created_at')->nullable()->comment('Fecha y hora de creacion del registro de la tabla orden_fotos.');
            $table->timestamp('updated_at')->nullable()->comment('Fecha y hora de ultima actualizacion del registro de la tabla orden_fotos.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_fotos');
    }
};
