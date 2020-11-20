<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEncuestaColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('encuesta', function (Blueprint $table) {
            $table->string('codigo_encuesta', 20)->nullable();
            $table->double('puntaje_paso_tres')->nullable();
            $table->double('puntaje_paso_cuatro')->nullable();
            $table->double('puntaje_paso_cinco')->nullable();
            $table->double('puntaje_paso_seis')->nullable();
            $table->double('puntaje_paso_siete')->nullable();
            $table->double('puntaje_paso_ocho')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
