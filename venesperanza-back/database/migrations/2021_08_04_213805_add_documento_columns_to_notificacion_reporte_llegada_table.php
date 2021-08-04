<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDocumentoColumnsToNotificacionReporteLlegadaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notificacion_reporte_llegada', function (Blueprint $table) {
            
            $table->string('tipo_documento', 50)->nullable()->default(NULL);
            $table->string('numero_documento', 20)->nullable()->default(NULL);
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
