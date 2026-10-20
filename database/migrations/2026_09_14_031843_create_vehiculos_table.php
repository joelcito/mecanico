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
        // @tabla vehiculos: Vehiculos de los clientes atendidos en el taller.
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->comment('Vehiculos de los clientes atendidos en el taller.');
            $table->id()->comment('Identificador unico de la tabla vehiculos.');
            $table->foreignId('cliente_id')->comment('Clave foranea que referencia a clientes.id. Cliente propietario del vehiculo.')->constrained('clientes')->restrictOnDelete();
            $table->foreignId('marca_id')->comment('Clave foranea que referencia a marcas.id. Marca asociada al registro.')->constrained('marcas')->restrictOnDelete();
            $table->string('modelo', 100)->comment('Modelo del vehiculo (tabla vehiculos).');
            $table->year('anio')->nullable()->comment('Anio del vehiculo (tabla vehiculos).');
            $table->string('placa', 20)->unique()->comment('Placa de identificacion del vehiculo (tabla vehiculos).');
            $table->string('color', 50)->nullable()->comment('Color del vehiculo (tabla vehiculos).');

            $table->enum('tipo_vehiculo', [
                'AUTOMOVIL',
                'MOTOCICLETA'
            ])->default('AUTOMOVIL')->comment('Clase de vehiculo; valores definidos en la declaracion enum (tabla vehiculos).');

            $table->string('vin', 50)->nullable()->unique()->comment('Numero de identificacion vehicular (VIN) (tabla vehiculos).');
            $table->string('numero_motor', 50)->nullable()->comment('Numero de identificacion del motor (tabla vehiculos).');
            $table->text('observaciones')->nullable()->comment('Notas adicionales del registro (tabla vehiculos).');
            $table->string('estado')->nullable()->comment('Estado del registro dentro de su proceso o catalogo (tabla vehiculos).');
            $table->foreignId('usuario_creador_id')->nullable()->comment('Clave foranea que referencia a users.id. Usuario que creo el registro.')->constrained('users')->nullOnDelete();
            $table->foreignId('usuario_modificador_id')->nullable()->comment('Clave foranea que referencia a users.id. Usuario de la ultima modificacion registrada.')->constrained('users')->nullOnDelete();
            $table->foreignId('usuario_eliminador_id')->nullable()->comment('Clave foranea que referencia a users.id. Usuario que registro la eliminacion.')->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->nullable()->comment('Fecha y hora de creacion del registro de la tabla vehiculos.');
            $table->timestamp('updated_at')->nullable()->comment('Fecha y hora de ultima actualizacion del registro de la tabla vehiculos.');
            $table->softDeletes()->comment('fecha y hora de eliminacion logica; nulo indica que no se ha eliminado (tabla vehiculos).');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
