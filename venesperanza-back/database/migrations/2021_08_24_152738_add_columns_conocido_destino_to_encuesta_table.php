<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsConocidoDestinoToEncuestaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('encuesta', function (Blueprint $table) {
            $table->string('telefono_conocido_destino',10)->nullable()->default(NULL);
            $table->string('parentesco_conocido_destino',50)->nullable()->default(NULL);
            $table->string('otro_parentesco_conocido_destino',200)->nullable()->default(NULL);
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
