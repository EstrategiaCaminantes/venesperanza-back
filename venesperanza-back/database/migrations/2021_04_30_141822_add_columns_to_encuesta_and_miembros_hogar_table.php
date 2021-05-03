<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToEncuestaAndMiembrosHogarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('encuesta', function (Blueprint $table) {
            $table->string('cual_otro_nacionalidad', 100)->nullable()->default(NULL);
            $table->integer('fuente')->nullable()->default(NULL);
            $table->tinyInteger('compartir_foto_documento_encuestado')->nullable()->default(NULL);
            $table->string('url_foto_documento_encuestado', 200)->nullable()->default(NULL);
            
        });

        Schema::table('miembros_hogar', function (Blueprint $table) {
            $table->integer('fuente')->nullable()->default(NULL);
           
            
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
