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
        // @tabla users: Usuarios del sistema y datos de acceso.
        Schema::create('users', function (Blueprint $table) {
            $table->comment('Usuarios del sistema y datos de acceso.');
            $table->id()->comment('Identificador unico de la tabla users.');



            $table->unsignedBigInteger('rol_id')->nullable()->comment('Clave foranea que referencia a roles.id. Rol asignado al usuario.');
            $table->unsignedBigInteger('sucursal_id')->nullable()->comment('Clave foranea que referencia a sucursales.id. Sucursal asociada al registro.');



            $table->string('nombres')->comment('Nombres de la persona (tabla users).');
            $table->string('ap_paterno')->nullable()->comment('Apellido paterno (tabla users).');
            $table->string('ap_materno')->nullable()->comment('Apellido materno (tabla users).');
            $table->string('cedula')->nullable()->comment('Numero del documento de identidad (tabla users).');

            $table->string('celular')->nullable()->comment('Numero de telefono celular (tabla users).');



            $table->string('email')->unique()->comment('Correo electronico de acceso (tabla users).');
            $table->string('password')->comment('Contrasena de acceso almacenada como hash (tabla users).');

            $table->timestamp('email_verified_at')->nullable()->comment('Fecha y hora de verificacion del correo electronico (tabla users).');

            $table->rememberToken()->comment('token utilizado por Laravel para recordar la sesion de acceso (tabla users).');



            $table->string('estado')->default('ACTIVO')->comment('Estado del registro dentro de su proceso o catalogo (tabla users).');



            $table->unsignedBigInteger('usuario_creador_id')->nullable()->comment('Clave foranea que referencia a users.id. Usuario que creo el registro.');
            $table->unsignedBigInteger('usuario_modificador_id')->nullable()->comment('Clave foranea que referencia a users.id. Usuario de la ultima modificacion registrada.');
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable()->comment('Clave foranea que referencia a users.id. Usuario que registro la eliminacion.');

            $table->dateTime('deleted_at')->nullable()->comment('Fecha y hora de eliminacion logica; nulo indica que no se ha eliminado (tabla users).');

            $table->timestamp('created_at')->nullable()->comment('Fecha y hora de creacion del registro de la tabla users.');
            $table->timestamp('updated_at')->nullable()->comment('Fecha y hora de ultima actualizacion del registro de la tabla users.');



            // @relacion rol_id: la cadena references/on define el destino y la regla de eliminacion, si se declara.
            $table->foreign('rol_id')
                ->references('id')
                ->on('roles')
                ->nullOnDelete();

            // @relacion sucursal_id: la cadena references/on define el destino y la regla de eliminacion, si se declara.
            $table->foreign('sucursal_id')
                ->references('id')
                ->on('sucursales')
                ->nullOnDelete();



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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};