<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // @tabla users: Usuarios del sistema y datos de acceso.
        Schema::table('users', function (Blueprint $table) {
            $table->string('nombres')->nullable()->change()->comment('Nombres de la persona (tabla users).');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nombres')->nullable(false)->change();
        });
    }
};