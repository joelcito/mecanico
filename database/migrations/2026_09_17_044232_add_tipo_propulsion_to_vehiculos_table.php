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
        Schema::table('vehiculos', function (Blueprint $table) {
            $table->enum('tipo_propulsion', [
                'COMBUSTION',
                'ELECTRICO',
                'HIBRIDO',
            ])
            ->nullable()
            ->after('tipo_vehiculo')->comment('Sistema de propulsion; valores definidos en la declaracion enum (tabla vehiculos).');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehiculos', function (Blueprint $table) {
            //
        });
    }
};
