<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pqs', function (Blueprint $table) {
            $table->bigInteger('id',true);
            $table->string('tipo_documento', 100)->nullable()->default(NULL);
            $table->string('numero_documento', 20)->nullable()->default(NULL);
            $table->string('nombre', 255)->nullable()->default(NULL);
            $table->string('apellido', 255)->nullable()->default(NULL);
            $table->string('mensaje',2000)->nullable()->default(NULL);
            $table->string('waId')->nullable()->default(NULL); //numero de WhatsApp desde el que el usuario escribe al chatbot
            $table->integer('pregunta')->nullable()->default(NULL); //Indica la pregunta en la encuesta del chatbot en el que está el usuario.
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
        Schema::dropIfExists('pqs');
    }
}
