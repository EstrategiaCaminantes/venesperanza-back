<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarriosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barrios', function (Blueprint $table) {
            $table->double('id')->nullable();
            $table->string('coddane_departamento', 11)->nullable();
            $table->integer('id_departamento')->nullable()->index('fk_departamento_barrio');
            $table->string('coddane_municipio', 11)->nullable();
            $table->integer('id_municipio')->nullable()->index('fk_municipio_barrio');
            $table->string('nombre', 100)->nullable();
            $table->string('tipo', 100)->nullable();
            $table->string('zona', 100)->nullable();
            $table->integer('sector')->nullable();
            $table->integer('seccion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('barrios');
    }
}
