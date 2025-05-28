<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('solicitud', function (Blueprint $table) {
            $table->text('contenido')->nullable()->after('asunto');
            $table->boolean('completada')->default(false)->after('firma_digital');
        });
    }


    public function down()
    {
        Schema::table('solicitud', function (Blueprint $table) {
            $table->dropColumn(['contenido', 'completada']);
        });
    }

};
