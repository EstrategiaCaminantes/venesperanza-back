<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Caminantes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('miembros_hogar', function (Blueprint $table) {
            $table->string('nacionalidad', 100)->nullable()->default(NULL);
            $table->string('cual_otro_nacionalidad', 100)->nullable()->default(NULL);
            $table->string('tipo_documento', 100)->nullable()->default(NULL);
            $table->string('cual_otro_tipo_documento', 100)->nullable()->default(NULL);
            $table->string('numero_documento', 20)->nullable()->default(NULL);
            $table->string('url_foto_documento', 200)->nullable()->default(NULL);
            $table->tinyInteger('compartir_foto_documento')->nullable()->default(NULL);
        });
        Schema::table('encuesta', function (Blueprint $table) {
            $table->string('primer_nombre', 50)->nullable()->default(NULL)->change();
            $table->string('primer_apellido', 50)->nullable()->default(NULL)->change();
            $table->string('nacionalidad', 50)->nullable()->default(NULL)->change();
            $table->string('tipo_documento', 50)->nullable()->default(NULL)->change();
            $table->string('fecha_llegada_pais', 100)->nullable()->default(NULL);
            $table->tinyInteger('estar_dentro_colombia')->nullable()->default(NULL);
            $table->string('pais_destino_final', 100)->nullable()->default(NULL);
            $table->string('cuenta_facebook', 100)->nullable()->default(NULL);
            $table->string('numero_alternativo', 50)->nullable()->default(NULL);
            $table->string('linea_contacto_alternativo', 50)->nullable()->default(NULL);
            $table->string('linea_alternativa_asociada_whatsapp', 50)->nullable()->default(NULL);
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
