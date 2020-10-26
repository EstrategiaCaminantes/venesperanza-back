<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToEncuestaNecesidadesBasicasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('encuesta_necesidades_basicas', function (Blueprint $table) {
            $table->foreign('id_encuesta', 'fk_encuesta_necesidad')->references('id')->on('encuesta')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('necesidad_basica_id', 'fk_necesidad_encuesta')->references('id')->on('necesidad_basica')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('encuesta_necesidades_basicas', function (Blueprint $table) {
            $table->dropForeign('fk_encuesta_necesidad');
            $table->dropForeign('fk_necesidad_encuesta');
        });
    }
}
