<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $productoCategoryIds = DB::table('productos')
            ->join('categorias', 'categorias.id', '=', 'productos.categoria_id')
            ->where('productos.tipo', 'PRODUCTO')
            ->where(function ($query) {
                $query->where('categorias.tipo', '!=', 'PRODUCTO')
                    ->orWhereNull('categorias.tipo');
            })
            ->distinct()
            ->pluck('productos.categoria_id');

        foreach ($productoCategoryIds as $categoryId) {
            $newCategoryId = $this->copyCategoryForType($categoryId, 'PRODUCTO');

            if ($newCategoryId) {
                DB::table('productos')
                    ->where('categoria_id', $categoryId)
                    ->where('tipo', 'PRODUCTO')
                    ->update(['categoria_id' => $newCategoryId]);
            }
        }

        $herramientaCategoryIds = DB::table('herramientas')
            ->join('categorias', 'categorias.id', '=', 'herramientas.categoria_id')
            ->where(function ($query) {
                $query->where('categorias.tipo', '!=', 'HERRAMIENTA')
                    ->orWhereNull('categorias.tipo');
            })
            ->distinct()
            ->pluck('herramientas.categoria_id');

        foreach ($herramientaCategoryIds as $categoryId) {
            $newCategoryId = $this->copyCategoryForType($categoryId, 'HERRAMIENTA');

            if ($newCategoryId) {
                DB::table('herramientas')
                    ->where('categoria_id', $categoryId)
                    ->update(['categoria_id' => $newCategoryId]);
            }
        }

        DB::table('categorias')
            ->where('tipo', 'AUTO')
            ->update(['tipo' => 'PRODUCTO']);

        Schema::table('categorias', function (Blueprint $table) {
            $table->string('tipo', 30)
                ->default('PRODUCTO')
                ->change();
        });
    }

    public function down(): void
    {
        throw new RuntimeException(
            'La normalización de categorías no se revierte automáticamente porque pudo crear categorías específicas por módulo.'
        );
    }

    private function copyCategoryForType(int $categoryId, string $type): ?int
    {
        $category = DB::table('categorias')->where('id', $categoryId)->first();

        if (!$category) {
            return null;
        }

        return DB::table('categorias')->insertGetId([
            'nombre' => $category->nombre,
            'descripcion' => $category->descripcion,
            'tipo' => $type,
            'estado' => $category->estado,
            'usuario_creador_id' => $category->usuario_creador_id,
            'usuario_modificador_id' => $category->usuario_modificador_id,
            'usuario_eliminador_id' => $category->usuario_eliminador_id,
            'deleted_at' => $category->deleted_at,
            'created_at' => $category->created_at,
            'updated_at' => $category->updated_at,
        ]);
    }
};