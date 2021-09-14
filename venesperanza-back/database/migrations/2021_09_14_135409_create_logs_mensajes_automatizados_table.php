<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsMensajesAutomatizadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs_mensajes_automatizados', function (Blueprint $table) {
            $table->bigInteger('id',true);
            $table->string('waId',255);
            $table->string('mensaje',255)->nullable()->default(NULL);
            $table->tinyInteger('tipo_mensaje')->nullable()->default(NULL);
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
        Schema::dropIfExists('logs_mensajes_automatizados');
    }
}
