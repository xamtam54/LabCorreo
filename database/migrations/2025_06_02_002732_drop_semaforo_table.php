<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('semaforo');
    }

    public function down()
    {
        // en caso de rollback,
        Schema::create('semaforo', function (Blueprint $table) {
            $table->id();
            $table->string('estado', 100);
            $table->integer('tiempo_restante_horas');
            $table->integer('plazo_horas');
            $table->foreignId('solicitud_id')->constrained('solicitud');
            $table->timestamps();
        });
    }
};
