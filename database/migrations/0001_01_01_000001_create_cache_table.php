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
        // @tabla cache: Valores de la cache de Laravel.
        Schema::create('cache', function (Blueprint $table) {
            $table->comment('Valores de la cache de Laravel.');
            $table->string('key')->primary()->comment('Clave de identificacion de la entrada de cache o bloqueo (tabla cache).');
            $table->mediumText('value')->comment('Contenido almacenado en cache (tabla cache).');
            $table->bigInteger('expiration')->index()->comment('Instante de expiracion expresado como tiempo Unix (tabla cache).');
        });

        // @tabla cache_locks: Bloqueos de la cache de Laravel.
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->comment('Bloqueos de la cache de Laravel.');
            $table->string('key')->primary()->comment('Clave de identificacion de la entrada de cache o bloqueo (tabla cache_locks).');
            $table->string('owner')->comment('Identificador del propietario del bloqueo (tabla cache_locks).');
            $table->bigInteger('expiration')->index()->comment('Instante de expiracion expresado como tiempo Unix (tabla cache_locks).');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
