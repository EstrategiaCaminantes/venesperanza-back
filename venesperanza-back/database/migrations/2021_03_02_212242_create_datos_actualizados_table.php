<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDatosActualizadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('datos_actualizados', function (Blueprint $table) {
            $table->bigInteger('id',true);
            $table->string('tipo_documento', 100)->nullable()->default(NULL);
            $table->string('numero_documento', 20)->nullable()->default(NULL);
            $table->string('telefono', 50)->nullable()->default(NULL);
            $table->string('correo_electronico', 50)->nullable()->default(NULL);
            $table->bigInteger('id_encuesta')->nullable()->index('fk_encuesta_datos_actualizados');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('datos_actualizados');
    }
}
