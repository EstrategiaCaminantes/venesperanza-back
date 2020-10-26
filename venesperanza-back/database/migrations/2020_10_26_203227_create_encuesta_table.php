<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEncuestaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('encuesta', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->integer('consentimiento_consentido')->nullable()->default(1);
            $table->string('primer_nombre', 500);
            $table->string('segundo_nombre', 500)->nullable();
            $table->string('primer_apellido', 500);
            $table->string('segundo_apellido', 500)->nullable();
            $table->string('sexo', 15)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('nacionalidad', 100);
            $table->string('tipo_documento', 100);
            $table->string('cual_otro_tipo_documento', 100)->nullable();
            $table->bigInteger('numero_documento')->nullable();
            $table->integer('id_departamento')->nullable()->index('fk_departamento');
            $table->integer('id_municipio')->nullable()->index('fk_municipo_encuesta');
            $table->string('barrio', 50)->nullable();
            $table->string('direccion', 100)->nullable();
            $table->bigInteger('numero_contacto')->nullable();
            $table->integer('linea_contacto_propia')->nullable()->default(1);
            $table->string('preguntar_en_caso_de_llamar', 100)->nullable();
            $table->integer('linea_asociada_whatsapp')->nullable()->default(0);
            $table->string('correo_electronico', 50)->nullable();
            $table->string('comentario', 200)->nullable();
            $table->integer('mujeres_embarazadas')->nullable()->default(0);
            $table->integer('mujeres_lactantes')->nullable()->default(0);
            $table->integer('situacion_discapacidad')->nullable()->default(0);
            $table->integer('enfermedades_cronicas')->nullable()->default(0);
            $table->integer('falta_comida')->nullable()->default(0);
            $table->string('cuantas_veces_falta_comida', 50)->nullable();
            $table->integer('dormir_sin_comer')->nullable()->default(0);
            $table->string('cuantas_veces_dormir_sin_comer', 50)->nullable();
            $table->integer('todo_dia_sin_comer')->nullable()->default(0);
            $table->string('cuantas_veces_todo_dia_sin_comer', 50)->nullable();
            $table->string('satisfaccion_necesidades_basicas', 50)->nullable();
            $table->string('tipo_vivienda_alojamiento_15_dias', 100)->nullable();
            $table->string('ingresos_c', 50)->nullable();
            $table->bigInteger('total_gastos')->nullable();
            $table->float('gastos_percapita1', 10, 0)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('deleted_at')->useCurrent();
            $table->string('paso', 20);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('encuesta');
    }
}
