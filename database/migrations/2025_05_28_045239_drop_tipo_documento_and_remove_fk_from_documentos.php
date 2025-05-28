<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('documento', function (Blueprint $table) {
            $table->dropForeign(['tipo_documento_id']);
            $table->dropColumn('tipo_documento_id');
        });

        Schema::dropIfExists('tipo_documento');
    }

    public function down()
    {
        Schema::create('tipo_documento', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->timestamps();
        });

        Schema::table('documento', function (Blueprint $table) {
            $table->foreignId('tipo_documento_id')->nullable()->constrained('tipo_documento')->nullOnDelete();
        });
    }
};
