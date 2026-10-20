<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // @tabla pagos: Pagos recibidos por las ordenes de servicio.
        Schema::create('pagos', function (Blueprint $table) {
            $table->comment('Pagos recibidos por las ordenes de servicio.');
            $table->id()->comment('Identificador unico de la tabla pagos.');

            $table->foreignId('orden_servicio_id')
                ->comment('Clave foranea que referencia a orden_servicios.id. Orden de servicio a la que pertenece el registro.')
                ->constrained('orden_servicios')
                ->restrictOnDelete();

            $table->foreignId('caja_id')
                ->nullable()
                ->comment('Clave foranea que referencia a cajas.id. Caja asociada al pago o movimiento.')
                ->constrained('cajas')
                ->nullOnDelete();

            $table->foreignId('sucursal_id')
                ->comment('Clave foranea que referencia a sucursales.id. Sucursal asociada al registro.')
                ->constrained('sucursales')
                ->restrictOnDelete();

            $table->decimal('monto', 12, 2)->comment('Importe del pago o movimiento (tabla pagos).');
            $table->decimal('cambio', 12, 2)->default(0)->comment('Importe devuelto al cliente como cambio (tabla pagos).');

            $table->dateTime('fecha')->comment('Fecha y hora del evento registrado (tabla pagos).');

            $table->string('tipo_pago', 30)->comment('Modalidad de pago registrada (tabla pagos).');

            $table->text('descripcion')->nullable()->comment('Descripcion del registro o concepto (tabla pagos).');

            $table->string('estado', 30)->default('ACTIVO')->comment('Estado del registro dentro de su proceso o catalogo (tabla pagos).');

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

            $table->softDeletes()->comment('fecha y hora de eliminacion logica; nulo indica que no se ha eliminado (tabla pagos).');
            $table->timestamp('created_at')->nullable()->comment('Fecha y hora de creacion del registro de la tabla pagos.');
            $table->timestamp('updated_at')->nullable()->comment('Fecha y hora de ultima actualizacion del registro de la tabla pagos.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};