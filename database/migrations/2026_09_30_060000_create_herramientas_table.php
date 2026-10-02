<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('herramientas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')
                ->nullable()
                ->constrained('categorias')
                ->restrictOnDelete();
            $table->foreignId('marca_id')
                ->nullable()
                ->constrained('marcas')
                ->restrictOnDelete();
            $table->string('codigo')->nullable();
            $table->string('nombre')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('unidad_medida')->nullable();
            $table->decimal('cantidad', 10, 2)->default(0);
            $table->decimal('stock_minimo', 10, 2)->default(0);
            $table->string('imagen')->nullable();
            $table->string('estado')->nullable();
            $table->foreignId('usuario_creador_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('usuario_modificador_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('usuario_eliminador_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();
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