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
        // @tabla roles: Roles de acceso de los usuarios.
        Schema::create('roles', function (Blueprint $table) {
            $table->comment('Roles de acceso de los usuarios.');
            $table->id()->comment('Identificador unico de la tabla roles.');

            $table->string('nombre')->unique()->comment('Nombre del registro (tabla roles).');
            $table->string('estado')->default('ACTIVO')->comment('Estado del registro dentro de su proceso o catalogo (tabla roles).');

            // Auditoría
            $table->unsignedBigInteger('usuario_creador_id')->nullable()->comment('Clave foranea que referencia a users.id. Usuario que creo el registro (tabla roles).');
            $table->unsignedBigInteger('usuario_modificador_id')->nullable()->comment('Clave foranea que referencia a users.id. Usuario de la ultima modificacion registrada (tabla roles).');
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable()->comment('Clave foranea que referencia a users.id. Usuario que registro la eliminacion (tabla roles).');

            $table->dateTime('deleted_at')->nullable()->comment('Fecha y hora de eliminacion logica; nulo indica que no se ha eliminado (tabla roles).');

            $table->timestamp('created_at')->nullable()->comment('Fecha y hora de creacion del registro de la tabla roles.');
            $table->timestamp('updated_at')->nullable()->comment('Fecha y hora de ultima actualizacion del registro de la tabla roles.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};