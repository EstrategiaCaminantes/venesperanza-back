<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificacionReporteLlegadaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notificacion_reporte_llegada', function (Blueprint $table) {
            $table->bigInteger('id',true);
            $table->integer('id_encuesta');
            $table->string('waId'); //numero de WhatsApp desde el que el usuario escribe al chatbot
            $table->string('respuesta')->nullable()->default(NULL); //numero de WhatsApp desde el que el usuario escribe al chatbot
            $table->tinyInteger('reenviar')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notificacion_reporte_llegada');
    }
}
