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
        // @tabla productos: Productos, repuestos e insumos del inventario.
        Schema::create('productos', function (Blueprint $table) {
            $table->comment('Productos, repuestos e insumos del inventario.');
            $table->id()->comment('Identificador unico de la tabla productos.');
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

            // @relacion categoria_id: la cadena references/on define el destino y la regla de eliminacion, si se declara.
            $table->foreign('categoria_id')
                ->references('id')
                ->on('categorias');

            $table->unsignedBigInteger('categoria_id')->nullable()->comment('Clave foranea que referencia a categorias.id. Categoria asociada al registro.');

            // @relacion marca_id: la cadena references/on define el destino y la regla de eliminacion, si se declara.
            $table->foreign('marca_id')
                ->references('id')
                ->on('marcas');

            $table->unsignedBigInteger('marca_id')->nullable()->comment('Clave foranea que referencia a marcas.id. Marca asociada al registro.');

            $table->string('codigo')->nullable()->comment('Codigo de identificacion en el catalogo (tabla productos).');
            $table->string('nombre')->nullable()->comment('Nombre del registro (tabla productos).');
            $table->string('descripcion')->nullable()->comment('Descripcion del registro o concepto (tabla productos).');

            $table->string('tipo')->nullable()->comment('Clasificacion del registro dentro de su modulo (tabla productos).');

            $table->string('unidad_medida')->nullable()->comment('Unidad en la que se expresa la cantidad (tabla productos).');

            $table->decimal('cantidad', 10, 2)->default(0)->comment('Cantidad registrada en la unidad de medida correspondiente (tabla productos).');
            $table->decimal('stock_minimo', 10, 2)->default(0)->comment('Existencia minima de referencia para reposicion (tabla productos).');

            $table->string('imagen')->nullable()->comment('Ruta o referencia de la imagen del catalogo (tabla productos).');

            $table->string('estado')->nullable()->comment('Estado del registro dentro de su proceso o catalogo (tabla productos).');
            $table->datetime('deleted_at')->nullable()->comment('Fecha y hora de eliminacion logica; nulo indica que no se ha eliminado (tabla productos).');

            $table->timestamp('created_at')->nullable()->comment('Fecha y hora de creacion del registro de la tabla productos.');
            $table->timestamp('updated_at')->nullable()->comment('Fecha y hora de ultima actualizacion del registro de la tabla productos.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
