<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLlegadasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('llegadas', function (Blueprint $table) {
            $table->bigInteger('id',true);
            $table->string('tipo_documento', 100)->nullable()->default(NULL);
            $table->string('numero_documento', 20)->nullable()->default(NULL);
            $table->string('telefono', 50)->nullable()->default(NULL);
            $table->integer('id_departamento')->nullable()->index('fk_departamento_llegadas');
            $table->integer('id_municipio')->nullable()->index('fk_municipo_llegadas');
            $table->bigInteger('id_encuesta')->nullable()->index('fk_encuesta_llegadas');
            $table->double('latitud')->nullable()->default(NULL);
            $table->double('longitud')->nullable()->default(NULL);
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
        Schema::dropIfExists('llegadas');
    }
}
