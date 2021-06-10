<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

use App\Models\Encuesta;
use App\Models\Autorizacion;
use App\Models\Municipio;


class FormulariosKobo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'formularioskobo:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consulta y registro de nuevos formularios Kobo';

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
        $endpoint = "https://kc.humanitarianresponse.info/api/v1/data/753415?format=json";
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $endpoint, ['query' => [
            /*'fields' => '["Este_evento_tiene_incidencia_e_001", "_13_Hechos",
            "_geolocation", "today", "categoria", "group_nw4pj90/N_mero_de_desaparecidos",
            "group_nw4pj90/N_mero_de_fallecidos", "Riesgos"]'*/
        ],'auth' => [
            //'snavarrete',
            env('KOBOUSER'),
            //'toxicity.1'
            env('KOBOPASSWORD')
        ]]);
        $statusCode = $response->getStatusCode();
        $formulariosKobo = json_decode($response->getBody(), true);
        

        $respuestasKobo = [];
        $totalkobo = 0;
        
        //foreach ($formulariosKobo as $encuestaKobo) {

                $encuesta_kobo_existe = Encuesta::where('fuente','=',3)->where('id_kobo','=',$formulariosKobo[51]['_id'])->first();

                if(!$encuesta_kobo_existe){
                    array_push( $respuestasKobo, $formulariosKobo[51]['_id']);
                    //cada kobo que no exista en la tabla encuesta de base datos, crea el registro nuevo con la info del kobo
                    //cada campo del kobo convertirlo al campo de la base datos
                    $nuevaEncuestaKobo = new Encuesta;

                    $nuevaEncuestaKobo->id_kobo = $formulariosKobo[51]['_id'];
                    $nuevaEncuestaKobo->fuente = 3;

                    if(isset($formulariosKobo[51]['Caracterizacion_GF/Fecha_llegada'])){
                        $nuevaEncuestaKobo->fecha_llegada_pais = $formulariosKobo[51]['Caracterizacion_GF/Fecha_llegada'];
                    }

                    if(isset($formulariosKobo[51]['Caracterizacion_GF/_En_qu_municipio_te_encuentras_ubicado'])){
                        $nuevaEncuestaKobo->ubicacion = $formulariosKobo[51]['Caracterizacion_GF/_En_qu_municipio_te_encuentras_ubicado'];
                    }

                    if(isset($formulariosKobo[51]['Caracterizacion_GF/_En_los_pr_ximos_seis_meses_pl'])){

                        if($formulariosKobo[51]['Caracterizacion_GF/_En_los_pr_ximos_seis_meses_pl'] === 's'){
                            $nuevaEncuestaKobo->estar_dentro_colombia = 1;
                        }else if($formulariosKobo[51]['Caracterizacion_GF/_En_los_pr_ximos_seis_meses_pl'] === 'no_estoy_seguro_a'){
                            $nuevaEncuestaKobo->estar_dentro_colombia = 2;
                        }else if($formulariosKobo[51]['Caracterizacion_GF/_En_los_pr_ximos_seis_meses_pl'] === 'no'){
                            $nuevaEncuestaKobo->estar_dentro_colombia = 0;
                        }
                    }

                    if(isset($formulariosKobo[51]['Caracterizacion_GF/_Cu_l_es_tu_destino_final_dent'])){

                        if($formulariosKobo[51]['Caracterizacion_GF/_Cu_l_es_tu_destino_final_dent'] === 'otro'){

                            $nuevaEncuestaKobo->nombre_municipio_destino_final = $formulariosKobo[51]['Caracterizacion_GF/_Cu_l'];

                        }else{

                            $municipioDestinoFinal = Municipio::where('nombre', '=', $formulariosKobo[51]['Caracterizacion_GF/_Cu_l_es_tu_destino_final_dent'])->first();

                            if($municipioDestinoFinal){
                                $nuevaEncuestaKobo->id_municipio_destino_final = $municipioDestinoFinal->id;
                            }else{
                                $nuevaEncuestaKobo->nombre_municipio_destino_final = $formulariosKobo[51]['Caracterizacion_GF/_Cu_l_es_tu_destino_final_dent'];

                            }
                        }
                        
                       
                    }

                    //return $nuevaEncuestaKobo;

                    $nuevaEncuestaKobo->save();

                    //Autorizacion
                    $autorizacion = new Autorizacion;

                    $autorizacion->id_encuesta = $nuevaEncuestaKobo->id;

                    if(isset($formulariosKobo[51]['Consentimiento/Autorizo_el_tratamiento_de_mis'])){

                        if($formulariosKobo[51]['Consentimiento/Autorizo_el_tratamiento_de_mis'] === 's'){
                            
                            $autorizacion->tratamiento_datos = 1;
                           
                        }else{
                            $autorizacion->tratamiento_datos = 0;
                        }
                        //return $kobo['Consentimiento/Autorizo_el_tratamiento_de_mis'];
                        $totalkobo += 1;
                    }

                    if(isset($formulariosKobo[51]['Consentimiento/Entiendo_y_acepto_lo_cipar_en_la_encuesta'])){

                        if($formulariosKobo[51]['Consentimiento/Entiendo_y_acepto_lo_cipar_en_la_encuesta'] === 's'){
                            $autorizacion->terminos_condiciones = 1;
                            $autorizacion->condiciones = 1;
                        }else{
                            $autorizacion->terminos_condiciones = 0; 
                            $autorizacion->condiciones = 0;
                        }
                    }

                    $autorizacion->save();

                    /*if($nuevaEncuestaKobo->save()){
                        array_push( $respuestasKobo, $nuevaEncuestaKobo['id_kobo']);
                        $totalkobo += 1;
                    };*/

                }
           

            
        //}
        //return $totalkobo;
        return $formulariosKobo[51];
    }
}
