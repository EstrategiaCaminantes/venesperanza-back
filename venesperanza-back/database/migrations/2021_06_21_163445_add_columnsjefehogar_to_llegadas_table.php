<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsjefehogarToLlegadasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('llegadas', function (Blueprint $table) {
            $table->string('nombre_jefe_hogar', 100)->nullable()->default(NULL); //nombre jefe hogar
            $table->renameColumn('telefono', 'numero_contacto'); //cambio telefono por numero_contacto
            $table->tinyInteger('numero_contacto_asociado_whatsapp')->nullable()->default(NULL);
            $table->string('donde_te_encuentras', 255)->nullable()->default(NULL);
            $table->string('otro_donde_te_encuentras', 255)->nullable()->default(NULL);


        });

        Schema::table('intentos', function (Blueprint $table) {
            $table->string('nombre_jefe_hogar', 100)->nullable()->default(NULL); //nombre jefe hogar
            $table->renameColumn('telefono', 'numero_contacto'); //cambio telefono por numero_contacto
            $table->tinyInteger('numero_contacto_asociado_whatsapp')->nullable()->default(NULL);
            $table->string('donde_te_encuentras', 255)->nullable()->default(NULL);
            $table->string('otro_donde_te_encuentras', 255)->nullable()->default(NULL);


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('llegadas', function (Blueprint $table) {
            //
        });
    }
}
