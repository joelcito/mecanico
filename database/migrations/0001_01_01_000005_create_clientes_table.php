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
        // @tabla clientes: Datos comerciales de los clientes vinculados a usuarios.
        Schema::create('clientes', function (Blueprint $table) {
            $table->comment('Datos comerciales de los clientes vinculados a usuarios.');
            $table->id()->comment('Identificador unico de la tabla clientes.');

            $table->unsignedBigInteger('user_id')->unique()->comment('Clave foranea que referencia a users.id. Usuario asociado al registro.');



            $table->string('nit')->nullable()->comment('Numero de identificacion tributaria del cliente (tabla clientes).');
            $table->string('razon_social')->nullable()->comment('Nombre o razon social para facturacion (tabla clientes).');
            $table->string('direccion')->nullable()->comment('Direccion registrada (tabla clientes).');



            $table->string('estado')->default('ACTIVO')->comment('Estado del registro dentro de su proceso o catalogo (tabla clientes).');


            $table->unsignedBigInteger('usuario_creador_id')->nullable()->comment('Clave foranea que referencia a users.id. Usuario que creo el registro.');
            $table->unsignedBigInteger('usuario_modificador_id')->nullable()->comment('Clave foranea que referencia a users.id. Usuario de la ultima modificacion registrada.');
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable()->comment('Clave foranea que referencia a users.id. Usuario que registro la eliminacion.');

            $table->dateTime('deleted_at')->nullable()->comment('Fecha y hora de eliminacion logica; nulo indica que no se ha eliminado (tabla clientes).');

            $table->timestamp('created_at')->nullable()->comment('Fecha y hora de creacion del registro de la tabla clientes.');
            $table->timestamp('updated_at')->nullable()->comment('Fecha y hora de ultima actualizacion del registro de la tabla clientes.');



            // @relacion user_id: la cadena references/on define el destino y la regla de eliminacion, si se declara.
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            // @relacion usuario_creador_id: la cadena references/on define el destino y la regla de eliminacion, si se declara.
            $table->foreign('usuario_creador_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            // @relacion usuario_modificador_id: la cadena references/on define el destino y la regla de eliminacion, si se declara.
            $table->foreign('usuario_modificador_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            // @relacion usuario_eliminador_id: la cadena references/on define el destino y la regla de eliminacion, si se declara.
            $table->foreign('usuario_eliminador_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};