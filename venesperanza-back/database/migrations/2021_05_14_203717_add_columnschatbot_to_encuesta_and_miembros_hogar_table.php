<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnschatbotToEncuestaAndMiembrosHogarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('encuesta', function (Blueprint $table) {
            $table->string('waId')->nullable()->default(NULL); //numero de WhatsApp desde el que el usuario escribe al chatbot
            $table->tinyInteger('conversation_start')->nullable()->default(NULL); //valor 0 si no hay conversación, 1 si hay conversación de chatbot
            $table->string('profileName')->nullable()->default(NULL); //nombre del usuario en WhatsApp
            $table->tinyInteger('encuesta_chatbot')->nullable()->default(NULL); //Indica si el usuario empezó a responder la encuesta en el chatbot
            $table->integer('paso_chatbot')->nullable()->default(NULL); //Indica el paso en la encuesta del chatbot en el que está el usuario.
            $table->integer('pregunta')->nullable()->default(NULL); //Indica la pregunta en la encuesta del chatbot en el que está el usuario.
            $table->integer('miembro_hogar_preguntando')->nullable()->default(NULL);  
            //Identifica para cuál Otro miembro de hogar se están respondiendo las preguntas del chatbot en el paso 2. Coincide con el campo chatbot_wapp_numero_miembro en la tabla ‘miembros_hogar’

            $table->string('nombre_municipio_destino_final')->nullable()->default(NULL); 
            //nombre del municipio de destino final en paso 1

            DB::statement("ALTER TABLE `encuesta` CHANGE `paso` `paso` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL;");
            //cambiar campo 'paso' a NULL, este campo no tiene valor cuando se crea desde chatbot o kobo, 
        });

        Schema::table('miembros_hogar', function (Blueprint $table) {
            $table->integer('numero_miembro')->nullable()->default(NULL); 
            // Sirve para identificar en el chatbot a cada uno de los Otros miembros de la familia que están con el encuestado-jefe de hogar. Y poder guardar la información correspondiente a cada miembro.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('encuesta', function (Blueprint $table) {
            //
        });
    }
}
