<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutorixzaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('autorizaciones', function (Blueprint $table) {
            $table->id();
            $table->boolean('tratamiento_datos')->nullable();
            $table->boolean('terminos_condiciones')->nullable();
            $table->boolean('condiciones')->nullable();
            $table->string('ip', 20)->nullable();
            $table->integer('id_encuesta')->nullable()->index('fk_encuesta');
            $table->softDeletes();
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
        Schema::dropIfExists('autorixzaciones');
    }
}
