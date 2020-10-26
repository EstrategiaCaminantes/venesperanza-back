<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToMiembrosHogarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('miembros_hogar', function (Blueprint $table) {
            $table->foreign('id_encuesta', 'fk_encuesta_miembro_hogar')->references('id')->on('encuesta')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('miembros_hogar', function (Blueprint $table) {
            $table->dropForeign('fk_encuesta_miembro_hogar');
        });
    }
}
