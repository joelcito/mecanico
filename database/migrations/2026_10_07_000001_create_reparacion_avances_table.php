<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // @tabla reparacion_avances: Bitacora de avances de las reparaciones.
        Schema::create('reparacion_avances', function (Blueprint $table) {
            $table->comment('Bitacora de avances de las reparaciones.');
            $table->id()->comment('Identificador unico de la tabla reparacion_avances.');
            $table->foreignId('reparacion_id')
                ->comment('Clave foranea que referencia a reparaciones.id. Reparacion asociada al registro.')
                ->constrained('reparaciones')
                ->restrictOnDelete();
            $table->foreignId('usuario_id')
                ->nullable()
                ->comment('Clave foranea que referencia a users.id. Usuario responsable del registro.')
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('tecnico_id')
                ->nullable()
                ->comment('Clave foranea que referencia a users.id. Usuario tecnico asociado al registro.')
                ->constrained('users')
                ->nullOnDelete();
            $table->dateTime('fecha')->comment('Fecha y hora del evento registrado (tabla reparacion_avances).');
            $table->text('descripcion')->comment('Descripcion del registro o concepto (tabla reparacion_avances).');
            $table->text('observaciones')->nullable()->comment('Notas adicionales del registro (tabla reparacion_avances).');
            $table->timestamp('created_at')->nullable()->comment('Fecha y hora de creacion del registro de la tabla reparacion_avances.');
            $table->timestamp('updated_at')->nullable()->comment('Fecha y hora de ultima actualizacion del registro de la tabla reparacion_avances.');

            // @indice: restriccion o indice sobre las columnas declaradas a continuacion.
            $table->index(['reparacion_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reparacion_avances');
    }
};