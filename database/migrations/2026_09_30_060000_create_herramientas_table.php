<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // @tabla herramientas: Catalogo e inventario de herramientas del taller.
        Schema::create('herramientas', function (Blueprint $table) {
            $table->comment('Catalogo e inventario de herramientas del taller.');
            $table->id()->comment('Identificador unico de la tabla herramientas.');
            $table->foreignId('categoria_id')
                ->nullable()
                ->comment('Clave foranea que referencia a categorias.id. Categoria asociada al registro.')
                ->constrained('categorias')
                ->restrictOnDelete();
            $table->foreignId('marca_id')
                ->nullable()
                ->comment('Clave foranea que referencia a marcas.id. Marca asociada al registro.')
                ->constrained('marcas')
                ->restrictOnDelete();
            $table->string('codigo')->nullable()->comment('Codigo de identificacion en el catalogo (tabla herramientas).');
            $table->string('nombre')->nullable()->comment('Nombre del registro (tabla herramientas).');
            $table->text('descripcion')->nullable()->comment('Descripcion del registro o concepto (tabla herramientas).');
            $table->string('unidad_medida')->nullable()->comment('Unidad en la que se expresa la cantidad (tabla herramientas).');
            $table->decimal('cantidad', 10, 2)->default(0)->comment('Cantidad registrada en la unidad de medida correspondiente (tabla herramientas).');
            $table->decimal('stock_minimo', 10, 2)->default(0)->comment('Existencia minima de referencia para reposicion (tabla herramientas).');
            $table->string('imagen')->nullable()->comment('Ruta o referencia de la imagen del catalogo (tabla herramientas).');
            $table->string('estado')->nullable()->comment('Estado del registro dentro de su proceso o catalogo (tabla herramientas).');
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
            $table->dateTime('deleted_at')->nullable()->comment('Fecha y hora de eliminacion logica; nulo indica que no se ha eliminado (tabla herramientas).');
            $table->timestamp('created_at')->nullable()->comment('Fecha y hora de creacion del registro de la tabla herramientas.');
            $table->timestamp('updated_at')->nullable()->comment('Fecha y hora de ultima actualizacion del registro de la tabla herramientas.');
        });

        DB::table('productos')
            ->where('tipo', 'HERRAMIENTA')
            ->orderBy('id')
            ->chunkById(100, function ($productos): void {
                foreach ($productos as $producto) {
                    DB::table('herramientas')->insert([
                        'id' => $producto->id,
                        'codigo' => $producto->codigo,
                        'nombre' => $producto->nombre,
                        'descripcion' => $producto->descripcion,
                        'categoria_id' => $producto->categoria_id,
                        'marca_id' => $producto->marca_id,
                        'unidad_medida' => $producto->unidad_medida,
                        'cantidad' => $producto->cantidad,
                        'stock_minimo' => $producto->stock_minimo,
                        'imagen' => $producto->imagen,
                        'estado' => $producto->estado,
                        'usuario_creador_id' => $producto->usuario_creador_id,
                        'usuario_modificador_id' => $producto->usuario_modificador_id,
                        'usuario_eliminador_id' => $producto->usuario_eliminador_id,
                        'deleted_at' => $producto->deleted_at,
                        'created_at' => $producto->created_at,
                        'updated_at' => $producto->updated_at,
                    ]);
                }
            });

            DB::table('productos')
                ->where('tipo', 'HERRAMIENTA')
                ->whereNull('deleted_at')
                ->update([
                    'deleted_at' => now(),
                    'estado' => 'INACTIVO',
                ]);
    }

    public function down(): void
    {
        $herramientasSinProductoOrigen = DB::table('herramientas')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('productos')
                    ->whereColumn('productos.id', 'herramientas.id')
                    ->where('productos.tipo', 'HERRAMIENTA');
            })
            ->exists();

        if ($herramientasSinProductoOrigen) {
            throw new RuntimeException(
                'No se puede revertir: existen herramientas creadas después de la migración.'
            );
        }

        DB::table('productos')
            ->join('herramientas', 'herramientas.id', '=', 'productos.id')
            ->where('productos.tipo', 'HERRAMIENTA')
            ->update([
                'productos.codigo' => DB::raw('herramientas.codigo'),
                'productos.nombre' => DB::raw('herramientas.nombre'),
                'productos.descripcion' => DB::raw('herramientas.descripcion'),
                'productos.categoria_id' => DB::raw('herramientas.categoria_id'),
                'productos.marca_id' => DB::raw('herramientas.marca_id'),
                'productos.unidad_medida' => DB::raw('herramientas.unidad_medida'),
                'productos.cantidad' => DB::raw('herramientas.cantidad'),
                'productos.stock_minimo' => DB::raw('herramientas.stock_minimo'),
                'productos.imagen' => DB::raw('herramientas.imagen'),
                'productos.estado' => DB::raw('herramientas.estado'),
                'productos.usuario_creador_id' => DB::raw('herramientas.usuario_creador_id'),
                'productos.usuario_modificador_id' => DB::raw('herramientas.usuario_modificador_id'),
                'productos.usuario_eliminador_id' => DB::raw('herramientas.usuario_eliminador_id'),
                'productos.deleted_at' => DB::raw('herramientas.deleted_at'),
                'productos.updated_at' => DB::raw('herramientas.updated_at'),
            ]);

        Schema::dropIfExists('herramientas');
    }
};