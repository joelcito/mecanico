<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orden_diagnosticos', function (Blueprint $table) {
            $table->foreignId('tecnico_id')
                ->nullable()
                ->after('usuario_diagnostico_id')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orden_diagnosticos', function (Blueprint $table) {
            $table->dropForeign(['tecnico_id']);
            $table->dropColumn('tecnico_id');
        });
    }
};