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
        // @tabla marcas: Marcas del catalogo.
        Schema::create('marcas', function (Blueprint $table) {
            $table->comment('Marcas del catalogo.');
            $table->id()->comment('Identificador unico de la tabla marcas.');
            // @relacion usuario_creador_id: la cadena references/on define el destino y la regla de eliminacion, si se declara.
            $table->foreign('usuario_creador_id')
                ->references('id')
                ->on('users');

            $table->unsignedBigInteger('usuario_creador_id')->nullable()->comment('Clave foranea que referencia a users.id. Usuario que creo el registro.');

            // @relacion usuario_modificador_id: la cadena references/on define el destino y la regla de eliminacion, si se declara.
            $table->foreign('usuario_modificador_id')
                ->references('id')
                ->on('users');

            $table->unsignedBigInteger('usuario_modificador_id')->nullable()->comment('Clave foranea que referencia a users.id. Usuario de la ultima modificacion registrada.');

            // @relacion usuario_eliminador_id: la cadena references/on define el destino y la regla de eliminacion, si se declara.
            $table->foreign('usuario_eliminador_id')
                ->references('id')
                ->on('users');

            $table->unsignedBigInteger('usuario_eliminador_id')->nullable()->comment('Clave foranea que referencia a users.id. Usuario que registro la eliminacion.');

            $table->string('nombre')->nullable()->comment('Nombre del registro (tabla marcas).');
            $table->string('descripcion')->nullable()->comment('Descripcion del registro o concepto (tabla marcas).');

            $table->string('estado')->nullable()->comment('Estado del registro dentro de su proceso o catalogo (tabla marcas).');
            $table->datetime('deleted_at')->nullable()->comment('Fecha y hora de eliminacion logica; nulo indica que no se ha eliminado (tabla marcas).');

            $table->timestamp('created_at')->nullable()->comment('Fecha y hora de creacion del registro de la tabla marcas.');
            $table->timestamp('updated_at')->nullable()->comment('Fecha y hora de ultima actualizacion del registro de la tabla marcas.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marcas');
    }
};
