<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // @tabla sucursales: Sucursales del taller.
        Schema::create('sucursales', function (Blueprint $table) {
            $table->comment('Sucursales del taller.');
            $table->id()->comment('Identificador unico de la tabla sucursales.');

            $table->string('nombre')->comment('Nombre del registro (tabla sucursales).');
            $table->string('direccion')->nullable()->comment('Direccion registrada (tabla sucursales).');
            $table->string('telefono')->nullable()->comment('Numero telefonico (tabla sucursales).');

            $table->string('estado')->default('ACTIVO')->comment('Estado del registro dentro de su proceso o catalogo (tabla sucursales).');

            // Auditoría
            $table->unsignedBigInteger('usuario_creador_id')->nullable()->comment('Clave foranea que referencia a users.id. Usuario que creo el registro (tabla sucursales).');
            $table->unsignedBigInteger('usuario_modificador_id')->nullable()->comment('Clave foranea que referencia a users.id. Usuario de la ultima modificacion registrada (tabla sucursales).');
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable()->comment('Clave foranea que referencia a users.id. Usuario que registro la eliminacion (tabla sucursales).');

            $table->dateTime('deleted_at')->nullable()->comment('Fecha y hora de eliminacion logica; nulo indica que no se ha eliminado (tabla sucursales).');

            $table->timestamp('created_at')->nullable()->comment('Fecha y hora de creacion del registro de la tabla sucursales.');
            $table->timestamp('updated_at')->nullable()->comment('Fecha y hora de ultima actualizacion del registro de la tabla sucursales.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sucursales');
    }
};