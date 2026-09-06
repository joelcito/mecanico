<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();



            $table->unsignedBigInteger('rol_id')->nullable();
            $table->unsignedBigInteger('sucursal_id')->nullable();



            $table->string('nombres');
            $table->string('ap_paterno')->nullable();
            $table->string('ap_materno')->nullable();
            $table->string('cedula')->nullable();

            $table->string('celular')->nullable();



            $table->string('email')->unique();
            $table->string('password');

            $table->timestamp('email_verified_at')->nullable();

            $table->rememberToken();



            $table->string('estado')->default('ACTIVO');



            $table->unsignedBigInteger('usuario_creador_id')->nullable();
            $table->unsignedBigInteger('usuario_modificador_id')->nullable();
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable();

            $table->dateTime('deleted_at')->nullable();

            $table->timestamps();



            $table->foreign('rol_id')
                ->references('id')
                ->on('roles')
                ->nullOnDelete();

            $table->foreign('sucursal_id')
                ->references('id')
                ->on('sucursales')
                ->nullOnDelete();



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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};