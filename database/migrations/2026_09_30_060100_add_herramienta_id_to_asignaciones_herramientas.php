<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asignaciones_herramientas', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);
            $table->unsignedBigInteger('producto_id')->nullable()->change();
        });

        Schema::table('asignaciones_herramientas', function (Blueprint $table) {
            $table->foreign('producto_id')
                ->references('id')
                ->on('productos')
                ->restrictOnDelete();
            $table->foreignId('herramienta_id')
                ->nullable()
                ->constrained('herramientas')
                ->restrictOnDelete();
        });

        DB::table('asignaciones_herramientas as asignaciones')
            ->join('productos', 'productos.id', '=', 'asignaciones.producto_id')
            ->where('productos.tipo', 'HERRAMIENTA')
            ->whereNull('asignaciones.herramienta_id')
            ->update([
                'asignaciones.herramienta_id' => DB::raw('asignaciones.producto_id'),
            ]);
    }

    public function down(): void
    {
        if (DB::table('asignaciones_herramientas')->whereNull('producto_id')->exists()) {
            throw new RuntimeException(
                'No se puede revertir mientras haya asignaciones ligadas solo a herramientas.'
            );
        }

        Schema::table('asignaciones_herramientas', function (Blueprint $table) {
            $table->dropForeign(['herramienta_id']);
            $table->dropColumn('herramienta_id');
            $table->dropForeign(['producto_id']);
            $table->unsignedBigInteger('producto_id')->nullable(false)->change();
        });

        Schema::table('asignaciones_herramientas', function (Blueprint $table) {
            $table->foreign('producto_id')
                ->references('id')
                ->on('productos')
                ->restrictOnDelete();
        });
    }
};