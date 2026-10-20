<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // @tabla asignaciones_herramientas: Asignaciones y devoluciones de herramientas a mecanicos.
        Schema::table('asignaciones_herramientas', function (Blueprint $table) {
            $table->dropForeign(['producto_id']);
            $table->unsignedBigInteger('producto_id')->nullable()->change()->comment('Producto asociado al registro (tabla asignaciones_herramientas).');
        });

        // @tabla asignaciones_herramientas: Asignaciones y devoluciones de herramientas a mecanicos.
        Schema::table('asignaciones_herramientas', function (Blueprint $table) {
            // @relacion producto_id: la cadena references/on define el destino y la regla de eliminacion, si se declara.
            $table->foreign('producto_id')
                ->references('id')
                ->on('productos')
                ->restrictOnDelete();
            $table->foreignId('herramienta_id')
                ->nullable()
                ->comment('Clave foranea que referencia a herramientas.id. Herramienta del catalogo asignada al mecanico.')
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