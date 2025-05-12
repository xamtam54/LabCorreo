<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50); // 3
            $table->timestamps();
        });

        Schema::table('usuarios', function (Blueprint $table) {
            $table->foreignId('rol_id')->nullable()->constrained('roles')->nullOnDelete();
            $table->boolean('bloqueado')->default(false);
        });

        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('descripcion')->nullable();
            $table->string('contrasena')->nullable(); // bcrypt
            $table->timestamps();
            $table->softDeletes();
        });

        // Tabla intermedia grupo-usuario
        Schema::create('grupo_usuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_id')->constrained('grupos')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->timestamps();
        });

        // Relación entre solicitudes y grupos
        Schema::table('solicitud', function (Blueprint $table) {
            $table->foreignId('grupo_id')->nullable()->constrained('grupos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('solicitud', function (Blueprint $table) {
            $table->dropForeign(['grupo_id']);
            $table->dropColumn('grupo_id');
        });

        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['rol_id']);
            $table->dropColumn('rol_id');
        });

        Schema::dropIfExists('grupo_usuario');
        Schema::dropIfExists('grupos');
        Schema::dropIfExists('roles');
    }
};
