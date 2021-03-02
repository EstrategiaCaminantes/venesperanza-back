<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('intentos', function (Blueprint $table) {
            $table->bigInteger('id',true);
            $table->string('tipo_documento', 100)->nullable()->default(NULL);
            $table->string('numero_documento', 20)->nullable()->default(NULL);
            $table->string('telefono', 50)->nullable()->default(NULL);
            $table->string('correo_electronico', 50)->nullable()->default(NULL);
            $table->integer('id_departamento')->nullable()->index('fk_departamento_intentos');
            $table->integer('id_municipio')->nullable()->index('fk_municipo_intentos');
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
        Schema::dropIfExists('intentos');
    }
}
