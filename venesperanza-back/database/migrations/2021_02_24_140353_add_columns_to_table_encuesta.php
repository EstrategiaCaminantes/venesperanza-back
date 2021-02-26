<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToTableEncuesta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('encuesta', function (Blueprint $table) {
            $table->string('numero_whatsapp_principal',20)->nullable()->default(NULL);
            $table->tinyInteger('tiene_cuenta_facebook')->nullable()->default(NULL);
            $table->tinyInteger('podemos_contactarte')->nullable()->default(NULL);
            $table->string('forma_contactarte')->nullable()->default(NULL);
            $table->string('otra_forma_contactarte')->nullable()->default(NULL);
            $table->integer('total_miembros_hogar')->unsigned()->nullable()->default(NULL);
            $table->string('donde_encontro_formulario',200)->nullable()->default(NULL);
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
