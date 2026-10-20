<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // @tabla movimientos_caja: Ingresos y egresos registrados en caja.
        Schema::create('movimientos_caja', function (Blueprint $table) {
            $table->comment('Ingresos y egresos registrados en caja.');
            $table->id()->comment('Identificador unico de la tabla movimientos_caja.');

            $table->foreignId('caja_id')
                ->comment('Clave foranea que referencia a cajas.id. Caja asociada al pago o movimiento.')
                ->constrained('cajas')
                ->restrictOnDelete();

            $table->foreignId('pago_id')
                ->nullable()
                ->comment('Clave foranea que referencia a pagos.id. Pago que origina el movimiento de caja.')
                ->constrained('pagos')
                ->nullOnDelete();

            $table->string('tipo', 30)->comment('Clasificacion del registro dentro de su modulo (tabla movimientos_caja).');

            $table->string('metodo_pago', 30)->nullable()->comment('Metodo de pago del movimiento de caja (tabla movimientos_caja).');

            $table->decimal('monto', 12, 2)->comment('Importe del pago o movimiento (tabla movimientos_caja).');

            $table->string('origen_dinero', 50)->nullable()->comment('Procedencia del dinero del movimiento (tabla movimientos_caja).');

            $table->text('descripcion')->nullable()->comment('Descripcion del registro o concepto (tabla movimientos_caja).');

            $table->dateTime('fecha')->comment('Fecha y hora del evento registrado (tabla movimientos_caja).');

            $table->string('estado', 30)->default('ACTIVO')->comment('Estado del registro dentro de su proceso o catalogo (tabla movimientos_caja).');

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

            $table->softDeletes()->comment('fecha y hora de eliminacion logica; nulo indica que no se ha eliminado (tabla movimientos_caja).');
            $table->timestamp('created_at')->nullable()->comment('Fecha y hora de creacion del registro de la tabla movimientos_caja.');
            $table->timestamp('updated_at')->nullable()->comment('Fecha y hora de ultima actualizacion del registro de la tabla movimientos_caja.');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_caja');
    }
};