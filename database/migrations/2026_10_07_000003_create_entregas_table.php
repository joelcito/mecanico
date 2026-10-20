<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // @tabla entregas: Registro de entrega del vehiculo por orden de servicio.
        Schema::create('entregas', function (Blueprint $table) {
            $table->comment('Registro de entrega del vehiculo por orden de servicio.');
            $table->id()->comment('Identificador unico de la tabla entregas.');
            $table->foreignId('orden_servicio_id')
                ->unique()
                ->comment('Clave foranea que referencia a orden_servicios.id. Orden de servicio a la que pertenece el registro.')
                ->constrained('orden_servicios')
                ->restrictOnDelete();
            $table->foreignId('usuario_id')
                ->nullable()
                ->comment('Clave foranea que referencia a users.id. Usuario responsable del registro.')
                ->constrained('users')
                ->nullOnDelete();
            $table->dateTime('fecha_entrega')->comment('Fecha y hora de entrega del vehiculo (tabla entregas).');
            $table->text('observaciones')->nullable()->comment('Notas adicionales del registro (tabla entregas).');
            $table->timestamp('created_at')->nullable()->comment('Fecha y hora de creacion del registro de la tabla entregas.');
            $table->timestamp('updated_at')->nullable()->comment('Fecha y hora de ultima actualizacion del registro de la tabla entregas.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entregas');
    }
};