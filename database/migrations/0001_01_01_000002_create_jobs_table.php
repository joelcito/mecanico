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
        // @tabla jobs: Trabajos pendientes de la cola de Laravel.
        Schema::create('jobs', function (Blueprint $table) {
            $table->comment('Trabajos pendientes de la cola de Laravel.');
            $table->id()->comment('Identificador unico de la tabla jobs.');
            $table->string('queue')->index()->comment('Nombre de la cola de procesamiento (tabla jobs).');
            $table->longText('payload')->comment('Contenido serializado del trabajo o de la sesion (tabla jobs).');
            $table->unsignedSmallInteger('attempts')->comment('Numero de intentos de ejecucion del trabajo (tabla jobs).');
            $table->unsignedInteger('reserved_at')->nullable()->comment('Instante de reserva del trabajo expresado como tiempo Unix (tabla jobs).');
            $table->unsignedInteger('available_at')->comment('Instante de disponibilidad del trabajo expresado como tiempo Unix (tabla jobs).');
            $table->unsignedInteger('created_at')->comment('Instante de creacion; el tipo declarado determina su representacion (tabla jobs).');
        });

        // @tabla job_batches: Lotes de trabajos en cola.
        Schema::create('job_batches', function (Blueprint $table) {
            $table->comment('Lotes de trabajos en cola.');
            $table->string('id')->primary()->comment('Identificador unico de la tabla job_batches.');
            $table->string('name')->comment('Nombre del lote de trabajos (tabla job_batches).');
            $table->integer('total_jobs')->comment('Numero total de trabajos del lote (tabla job_batches).');
            $table->integer('pending_jobs')->comment('Numero de trabajos pendientes del lote (tabla job_batches).');
            $table->integer('failed_jobs')->comment('Numero de trabajos fallidos del lote (tabla job_batches).');
            $table->longText('failed_job_ids')->comment('Identificadores de los trabajos fallidos del lote (tabla job_batches).');
            $table->mediumText('options')->nullable()->comment('Opciones serializadas del lote (tabla job_batches).');
            $table->integer('cancelled_at')->nullable()->comment('Instante de cancelacion del lote expresado como tiempo Unix (tabla job_batches).');
            $table->integer('created_at')->comment('Instante de creacion; el tipo declarado determina su representacion (tabla job_batches).');
            $table->integer('finished_at')->nullable()->comment('Instante de finalizacion del lote expresado como tiempo Unix (tabla job_batches).');
        });

        // @tabla failed_jobs: Trabajos en cola que terminaron con error.
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->comment('Trabajos en cola que terminaron con error.');
            $table->id()->comment('Identificador unico de la tabla failed_jobs.');
            $table->string('uuid')->unique()->comment('Identificador unico del trabajo fallido (tabla failed_jobs).');
            $table->string('connection')->comment('Conexion utilizada por la cola (tabla failed_jobs).');
            $table->string('queue')->comment('Nombre de la cola de procesamiento (tabla failed_jobs).');
            $table->longText('payload')->comment('Contenido serializado del trabajo o de la sesion (tabla failed_jobs).');
            $table->longText('exception')->comment('Detalle del error que provoco el fallo del trabajo (tabla failed_jobs).');
            $table->timestamp('failed_at')->useCurrent()->comment('Fecha y hora del fallo del trabajo (tabla failed_jobs).');

            // @indice: restriccion o indice sobre las columnas declaradas a continuacion.
            $table->index(['connection', 'queue', 'failed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
