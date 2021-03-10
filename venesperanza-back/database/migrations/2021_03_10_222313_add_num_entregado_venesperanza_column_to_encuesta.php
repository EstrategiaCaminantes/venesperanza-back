<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNumEntregadoVenesperanzaColumnToEncuesta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('encuesta', function (Blueprint $table) {
            $table->tinyInteger('numero_entregado_venesperanza')->nullable()->default(NULL);

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
