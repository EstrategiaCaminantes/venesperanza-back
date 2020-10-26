<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToEncuestaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('encuesta', function (Blueprint $table) {
            $table->foreign('id_departamento', 'fk_departamento_encuesta')->references('id')->on('departamento')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('id_municipio', 'fk_municipo_encuesta')->references('id')->on('municipio')->onUpdate('RESTRICT')->onDelete('RESTRICT');
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
            $table->dropForeign('fk_departamento_encuesta');
            $table->dropForeign('fk_municipo_encuesta');
        });
    }
}
