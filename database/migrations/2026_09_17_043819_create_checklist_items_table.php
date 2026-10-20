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
        // @tabla checklist_items: Catalogo de puntos de inspeccion por vehiculo y propulsion.
        Schema::create('checklist_items', function (Blueprint $table) {
            $table->comment('Catalogo de puntos de inspeccion por vehiculo y propulsion.');
            $table->id()->comment('Identificador unico de la tabla checklist_items.');

            $table->string('nombre', 150)->comment('Nombre del registro (tabla checklist_items).');

            $table->enum('tipo_vehiculo', [
                'AUTOMOVIL',
                'MOTOCICLETA',
            ])->comment('Clase de vehiculo; valores definidos en la declaracion enum (tabla checklist_items).');

            $table->enum('tipo_propulsion', [
                'COMBUSTION',
                'ELECTRICO',
                'HIBRIDO',
            ])->nullable()->comment('Sistema de propulsion; valores definidos en la declaracion enum (tabla checklist_items).');

            $table->unsignedInteger('orden')->default(0)->comment('Posicion de presentacion del punto de inspeccion (tabla checklist_items).');

            $table->string('estado', 30)->default('ACTIVO')->comment('Estado del registro dentro de su proceso o catalogo (tabla checklist_items).');

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

            $table->softDeletes()->comment('fecha y hora de eliminacion logica; nulo indica que no se ha eliminado (tabla checklist_items).');
            $table->timestamp('created_at')->nullable()->comment('Fecha y hora de creacion del registro de la tabla checklist_items.');
            $table->timestamp('updated_at')->nullable()->comment('Fecha y hora de ultima actualizacion del registro de la tabla checklist_items.');

            // @indice: restriccion o indice sobre las columnas declaradas a continuacion.
            $table->index([
                'tipo_vehiculo',
                'tipo_propulsion',
                'estado',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_items');
    }
};
