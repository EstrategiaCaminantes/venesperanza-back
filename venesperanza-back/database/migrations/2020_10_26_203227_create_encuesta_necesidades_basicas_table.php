<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEncuestaNecesidadesBasicasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('encuesta_necesidades_basicas', function (Blueprint $table) {
            $table->bigInteger('id_encuesta')->index('fk_encuesta_necesidad');
            $table->integer('necesidad_basica_id')->index('fk_necesidad_encuesta');
            $table->timestamps(0);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('encuesta_necesidades_basicas');
    }
}
