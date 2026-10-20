<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // @tabla roles: Roles de acceso de los usuarios.
        Schema::table('roles', function (Blueprint $table) {
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

        // @tabla sucursales: Sucursales del taller.
        Schema::table('sucursales', function (Blueprint $table) {
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

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropForeign(['usuario_creador_id']);
            $table->dropForeign(['usuario_modificador_id']);
            $table->dropForeign(['usuario_eliminador_id']);
        });

        Schema::table('sucursales', function (Blueprint $table) {
            $table->dropForeign(['usuario_creador_id']);
            $table->dropForeign(['usuario_modificador_id']);
            $table->dropForeign(['usuario_eliminador_id']);
        });
    }
};