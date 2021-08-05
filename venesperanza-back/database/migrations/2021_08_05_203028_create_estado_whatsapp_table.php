<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstadoWhatsappTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estado_whatsapp', function (Blueprint $table) {
            $table->bigInteger('id',true);
            $table->string('message_id',500)->nullable();
            $table->string('message_status',100)->nullable();
            $table->string('error_code',20)->nullable(); 
            $table->string('error_description',2000)->nullable();
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
        Schema::dropIfExists('estado_whatsapp');
    }
}
