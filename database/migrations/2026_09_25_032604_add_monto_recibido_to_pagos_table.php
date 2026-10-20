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
        // @tabla pagos: Pagos recibidos por las ordenes de servicio.
        Schema::table('pagos', function (Blueprint $table) {
            $table->decimal('monto_recibido', 12, 2)
                ->default(0)
                ->after('monto')->comment('Importe entregado por el cliente antes de descontar el cambio (tabla pagos).');
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropColumn('monto_recibido');
        });
    }
};
