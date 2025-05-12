<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('grupo_usuario', function (Blueprint $table) {
            $table->boolean('es_administrador')->default(false);
            $table->boolean('bloqueado')->default(false);
        });

        // agregar la columna creador_id sin restricción
        Schema::table('grupos', function (Blueprint $table) {
            $table->unsignedBigInteger('creador_id')->nullable()->after('id');
            $table->string('codigo', 20)->unique()->after('contrasena');
        });

        // agregar la restricción de clave foranea por separado
        Schema::table('grupos', function (Blueprint $table) {
            $table->foreign('creador_id')->references('id')->on('usuarios')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('grupo_usuario', function (Blueprint $table) {
            $table->dropColumn(['es_administrador', 'bloqueado']);
        });

        Schema::table('grupos', function (Blueprint $table) {
            $table->dropForeign(['creador_id']);
            $table->dropColumn('creador_id');

            $table->dropUnique(['codigo']);
            $table->dropColumn('codigo');
        });
    }

};
