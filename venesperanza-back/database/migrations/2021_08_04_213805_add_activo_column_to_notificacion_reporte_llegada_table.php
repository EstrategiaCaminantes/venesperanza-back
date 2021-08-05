<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActivoColumnToNotificacionReporteLlegadaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notificacion_reporte_llegada', function (Blueprint $table) {
            
            $table->tinyInteger('activo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notificacion_reporte_llegada', function (Blueprint $table) {
            //
        });
    }
}
