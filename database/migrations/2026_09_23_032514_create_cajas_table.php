<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // @tabla cajas: Aperturas, cierres y totales de caja por sucursal.
        Schema::create('cajas', function (Blueprint $table) {
            $table->comment('Aperturas, cierres y totales de caja por sucursal.');
            $table->id()->comment('Identificador unico de la tabla cajas.');

            $table->foreignId('usuario_id')
                ->comment('Clave foranea que referencia a users.id. Usuario responsable del registro.')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('sucursal_id')
                ->comment('Clave foranea que referencia a sucursales.id. Sucursal asociada al registro.')
                ->constrained('sucursales')
                ->restrictOnDelete();

            $table->decimal('total_ingresos', 12, 2)->default(0)->comment('Importe acumulado de ingresos de la caja (tabla cajas).');
            $table->decimal('total_egresos', 12, 2)->default(0)->comment('Importe acumulado de egresos de la caja (tabla cajas).');

            $table->decimal('monto_apertura', 12, 2)->default(0)->comment('Importe inicial al abrir la caja (tabla cajas).');
            $table->decimal('monto_cierre', 12, 2)->nullable()->comment('Importe registrado al cerrar la caja (tabla cajas).');

            $table->dateTime('fecha_apertura')->comment('Fecha y hora de apertura de la caja (tabla cajas).');
            $table->dateTime('fecha_cierre')->nullable()->comment('Fecha y hora de cierre de la caja (tabla cajas).');

            $table->string('estado', 30)->default('ABIERTA')->comment('Estado del registro dentro de su proceso o catalogo (tabla cajas).');

            // Auditoría
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

            $table->softDeletes()->comment('fecha y hora de eliminacion logica; nulo indica que no se ha eliminado (tabla cajas).');
            $table->timestamp('created_at')->nullable()->comment('Fecha y hora de creacion del registro de la tabla cajas.');
            $table->timestamp('updated_at')->nullable()->comment('Fecha y hora de ultima actualizacion del registro de la tabla cajas.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};