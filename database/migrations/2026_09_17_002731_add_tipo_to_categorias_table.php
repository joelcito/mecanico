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
        // @tabla categorias: Categorias del catalogo de productos y herramientas.
        Schema::table('categorias', function (Blueprint $table) {
             $table->string('tipo', 30)
                ->default('AUTO')
                ->after('descripcion')->comment('Clasificacion del registro dentro de su modulo (tabla categorias).');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
};
