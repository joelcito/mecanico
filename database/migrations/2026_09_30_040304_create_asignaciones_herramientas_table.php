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
        // @tabla asignaciones_herramientas: Asignaciones y devoluciones de herramientas a mecanicos.
        Schema::create('asignaciones_herramientas', function (Blueprint $table) {
            $table->comment('Asignaciones y devoluciones de herramientas a mecanicos.');
            $table->id()->comment('Identificador unico de la tabla asignaciones_herramientas.');
            $table->foreignId('producto_id')
                ->comment('Clave foranea que referencia a productos.id. Producto asociado al registro.')
                ->constrained('productos')
                ->restrictOnDelete();
            $table->foreignId('mecanico_id')
                ->comment('Clave foranea que referencia a users.id. Usuario mecanico que recibe la herramienta.')
                ->constrained('users')
                ->restrictOnDelete();
            $table->decimal('cantidad', 12, 2)
                ->default(1)->comment('Cantidad registrada en la unidad de medida correspondiente (tabla asignaciones_herramientas).');
            $table->dateTime('fecha_asignacion')->comment('Fecha y hora de asignacion de la herramienta (tabla asignaciones_herramientas).');
            $table->dateTime('fecha_devolucion')->nullable()->comment('Fecha y hora de devolucion de la herramienta (tabla asignaciones_herramientas).');
            $table->string('estado', 30)->default('ASIGNADA')->comment('Estado del registro dentro de su proceso o catalogo (tabla asignaciones_herramientas).');
            $table->text('observaciones')->nullable()->comment('Notas adicionales del registro (tabla asignaciones_herramientas).');
            $table->foreignId('usuario_creador_id')
                ->nullable()
                ->comment('Clave foranea que referencia a users.id. Usuario que creo el registro.')
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('usuario_modificador_id')
                ->nullable()
                ->comment('Clave foranea que referencia a users.id. Usuario de la ultima modificacion registrada.')
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('usuario_eliminador_id')
                ->nullable()
                ->comment('Clave foranea que referencia a users.id. Usuario que registro la eliminacion.')
                ->constrained('users')
                ->nullOnDelete();
            $table->softDeletes()->comment('fecha y hora de eliminacion logica; nulo indica que no se ha eliminado (tabla asignaciones_herramientas).');
            $table->timestamp('created_at')->nullable()->comment('Fecha y hora de creacion del registro de la tabla asignaciones_herramientas.');
            $table->timestamp('updated_at')->nullable()->comment('Fecha y hora de ultima actualizacion del registro de la tabla asignaciones_herramientas.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignaciones_herramientas');
    }
};
