<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->foreign('usuario_creador_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('usuario_modificador_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('usuario_eliminador_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });

        Schema::table('sucursales', function (Blueprint $table) {
            $table->foreign('usuario_creador_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('usuario_modificador_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

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