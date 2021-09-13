<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Encuesta;
use App\Models\Llegadas;
use App\Models\DatosActualizados;

class ActualizarLlegadasYDatosActualizados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'actualizarLlegadasYDatosActualizados:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza registros de Llegadas y Datos Actualizados que tienen id_encuesta = NULL';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
                //Busca las llegadas donde 'id_encuesta' = NULL
                $llegadasSinIdEncuesta = Llegadas::whereNull('id_encuesta')->get();
        
                //$llegadasActualizadas = [];
                foreach ($llegadasSinIdEncuesta as $llegada) {
                    
                    //Devuelve la ultima Encuesta creada donde tipo_documento = llegada->documento y
                    // numero_documento = llegada->numero_documento
                    $encuesta = Encuesta::where('tipo_documento','=',$llegada->tipo_documento)
                    ->where('numero_documento','=',$llegada->numero_documento)->latest()->first();
        
                    if($encuesta){
                        //Si encuesta existe, asigna el id de la Encuesta al campo id_encuesta de Llegada
                        //y actualiza llegada.
                        
                        $llegada->id_encuesta = $encuesta->id;
                        $llegada->save();
                        //$llegadasActualizadas[]=$llegada;
                    }
                }
                //return sizeof($llegadasActualizadas);
        
        
                //Busca lso registros de Datos Actualizados que tengan id_encuesta = NULL
                $actualizarDatosSinIdEncuesta = DatosActualizados::whereNull('id_encuesta')->get();
                
                $NuevasActualizarDatos = [];
        
                foreach ($actualizarDatosSinIdEncuesta as $datos_actualizados) {
                    
                    //Devuelve la ultima Encuesta creada donde tipo_documento = datos_actualziados->documento y
                    // numero_documento = datos_actualizados->numero_documento
                    $encuesta = Encuesta::where('tipo_documento','=',$datos_actualizados->tipo_documento)
                    ->where('numero_documento','=',$datos_actualizados->numero_documento)->latest()->first();
        
                    if($encuesta){
                        //Si encuesta existe, asigna el id de la Encuesta al campo id_encuesta de datos_actualziados
                        //y actualiza datos_actualziados.
                        
                        $datos_actualizados->id_encuesta = $encuesta->id;
                        $datos_actualizados->save();
                        //$NuevasActualizarDatos[]=$registro;
                    }
                }
                //return $NuevasActualizarDatos;
                //return sizeof($NuevasActualizarDatos);
    }
}
