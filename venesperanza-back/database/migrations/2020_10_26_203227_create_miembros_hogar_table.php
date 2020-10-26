<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMiembrosHogarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('miembros_hogar', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('id_encuesta')->nullable()->index('fk_encuesta_miembro_hogar');
            $table->string('primer_nombre_miembro', 500)->nullable();
            $table->string('segundo_nombre_miembro', 500)->nullable();
            $table->string('primer_apellido_miembro', 500)->nullable();
            $table->string('segundo_apellido_miembro', 500)->nullable();
            $table->string('sexo_miembro', 15)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();
            $table->softDeletes()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('miembros_hogar');
    }
}
