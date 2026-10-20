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
        // @tabla sessions: Sesiones de acceso almacenadas por Laravel.
        Schema::create('sessions', function (Blueprint $table) {
            $table->comment('Sesiones de acceso almacenadas por Laravel.');
            $table->string('id')->primary()->comment('Identificador unico de la tabla sessions.');
            $table->foreignId('user_id')->nullable()->index()->comment('Usuario asociado al registro (tabla sessions).');
            $table->string('ip_address', 45)->nullable()->comment('Direccion IP desde la que se accedio a la sesion (tabla sessions).');
            $table->text('user_agent')->nullable()->comment('Identificacion del navegador o cliente de la sesion (tabla sessions).');
            $table->longText('payload')->comment('Contenido serializado del trabajo o de la sesion (tabla sessions).');
            $table->integer('last_activity')->index()->comment('Instante de ultima actividad de la sesion expresado como tiempo Unix (tabla sessions).');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
