<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversacionChatbotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conversacion_chatbot', function (Blueprint $table) {
            $table->bigInteger('id',true);
            $table->string('waId'); //numero de WhatsApp desde el que el usuario escribe al chatbot
            $table->string('profileName'); //nombre de usuario en WhatsApp
            $table->tinyInteger('conversation_start')->nullable()->default(NULL); //valor 0 si no hay conversación, 1 si hay conversación de chatbot
            $table->tinyInteger('autorizacion')->nullable();
            $table->tinyInteger('tipo_formulario')->nullable();
            $table->timestamps();

        });

        Schema::table('llegadas', function (Blueprint $table) {
            $table->string('waId')->nullable()->default(NULL); //numero de WhatsApp desde el que el usuario escribe al chatbot
            $table->integer('pregunta')->nullable()->default(NULL); //Indica la pregunta en la encuesta del chatbot en el que está el usuario.

        });

        Schema::table('datos_actualizados', function (Blueprint $table) {
            $table->string('waId')->nullable()->default(NULL); //numero de WhatsApp desde el que el usuario escribe al chatbot
            $table->integer('pregunta')->nullable()->default(NULL); //Indica la pregunta en la encuesta del chatbot en el que está el usuario.

        });

        Schema::table('autorizaciones', function (Blueprint $table) {
            $table->string('waId')->nullable()->default(NULL); //numero de WhatsApp desde el que el usuario escribe al chatbot

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conversacion_chatbot');
    }
}
