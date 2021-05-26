<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Models\Encuesta;
use App\Models\MiembrosHogar;
use App\Models\Autorizacion;
use App\Models\Webhook;
use App\Models\NecesidadBasica;

use DateTime;

use Illuminate\Support\Facades\Storage;


class KoboController extends Controller
{

    public function index()
    {
        //https://kc.humanitarianresponse.info/api/v1/
        //https://kc.humanitarianresponse.info/api/v1/data?format=json
        //Lista de formularios: https://kc.humanitarianresponse.info/api/v1/data/753415?format=json
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
        
        foreach ($encuestasKobo as $encuestaKobo) {

                $encuesta_kobo_existe = Encuesta::where('fuente','=',3)->where('id_kobo','=',$kobo['_id'])->first();

                if(!$encuesta_kobo_existe){
                    array_push( $respuestasKobo, $kobo['_id']);
                    //cada kobo que no exista en la tabla encuesta de base datos, crea el registro nuevo con la info del kobo
                    //cada campo del kobo convertirlo al campo de la base datos
                    $nuevaEncuestaKobo = new Encuesta;

                    $nuevaEncuestaKobo->id_kobo = $kobo['_id'];
                    $nuevaEncuestaKobo->fuente = 3;

                    if(isset($encuestaKobo['Caracterización Grupo Familiar/¿En qué fecha tu y tu grupo'])){
                        $nuevaEncuestaKobo->fecha_llegada_pais = $encuestaKobo['Caracterización Grupo Familiar/¿En qué fecha tu y tu grupo'];
                    }

                    if(isset($encuestaKobo['Caracterizacion_GF/_En_qu_municipio_te_encuentras_ubicado'])){
                        $nuevaEncuestaKobo->ubicacion = $encuestaKobo['Caracterizacion_GF/_En_qu_municipio_te_encuentras_ubicado'];
                    }

                    if(isset($encuestaKobo['Caracterizacion_GF/_En_los_pr_ximos_seis_meses_pl'])){

                        if($encuestaKobo['Caracterizacion_GF/_En_los_pr_ximos_seis_meses_pl'] === 's'){
                            $nuevaEncuestaKobo->estar_dentro_colombia = 1;
                        }else if($encuestaKobo['Caracterizacion_GF/_En_los_pr_ximos_seis_meses_pl'] === 'no_estoy_seguro_a'){
                            $nuevaEncuestaKobo->estar_dentro_colombia = 2;
                        }else if($encuestaKobo['Caracterizacion_GF/_En_los_pr_ximos_seis_meses_pl'] === 'no'){
                            $nuevaEncuestaKobo->estar_dentro_colombia = 0;
                        }
                    }

                    if(isset($encuestaKobo['Caracterizacion_GF/_Cu_l_es_tu_destino_final_dent'])){

                        if(isset($encuestaKobo['Caracterizacion_GF/_Cu_l_es_tu_destino_final_dent'] === 'Otro'){

                            $nuevaEncuestaKobo->nombre_municipio_destino_final = $encuestaKobo['Caracterizacion_GF/_En_qu_municipio_te_encuentras_ubicado'];

                        }else{

                            $municipioDestinoFinal = Municipio::where('nombre', '=', $encuestaKobo['Caracterizacion_GF/_Cu_l_es_tu_destino_final_dent'])->first();

                            if($municipioDestinoFinal){
                                $nuevaEncuestaKobo->id_municipio_destino_final = $municipioDestinoFinal->id;
                            }else{
                                $nuevaEncuestaKobo->nombre_municipio_destino_final = $encuestaKobo['Caracterizacion_GF/_Cu_l_es_tu_destino_final_dent'];

                            }
                        }
                        
                       
                    }


                    $nuevaEncuestaKobo->save();

                    //Autorizacion
                    $autorizacion = new Autorizacion;

                    $autorizacion->id_encuesta = $nuevaEncuestaKobo->id;

                    if(isset($encuestaKobo['Consentimiento/Autorizo_el_tratamiento_de_mis'])){

                        if($encuestaKobo['Consentimiento/Autorizo_el_tratamiento_de_mis'] === 's'){
                            
                            $autorizacion->tratamiento_datos = 1;
                           
                        }else{
                            $autorizacion->tratamiento_datos = 0;
                        }
                        //return $kobo['Consentimiento/Autorizo_el_tratamiento_de_mis'];
                        $totalkobo += 1;
                    }

                    if(isset($encuestaKobo['Consentimiento/Entiendo_y_acepto_lo_cipar_en_la_encuesta'])){

                        if($encuestaKobo['Consentimiento/Entiendo_y_acepto_lo_cipar_en_la_encuesta'] === 's'){
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
           

            
        }
        return $totalkobo;

        //Prueba individual
        /*
        if(count($formulariosKobo) > 0){

        
            $respuesta_kobo_existe1 = Encuesta::where('fuente','=',3)->where('id_kobo','=',$formulariosKobo[0]['_id'])->first();
            $respuesta_kobo_existe2 = Encuesta::where('fuente','=',3)->where('id_kobo','=',$formulariosKobo[3]['_id'])->first();

            if(!$respuesta_kobo_existe1){
                $nuevaEncuestaKobo1 = new Encuesta;
                $nuevaEncuestaKobo1->id_kobo = $formulariosKobo[0]['_id']; 
                $nuevaEncuestaKobo1->fuente = 3;
                if($nuevaEncuestaKobo1->save()){
                    array_push( $respuestasKobo, $nuevaEncuestaKobo1['id_kobo']);
                };
            }

            if(!$respuesta_kobo_existe2){
                $nuevaEncuestaKobo2 = new Encuesta;
                $nuevaEncuestaKobo2->id_kobo = $formulariosKobo[3]['_id']; 
                $nuevaEncuestaKobo2->fuente = 3;
                if($nuevaEncuestaKobo2->save()){
                    array_push( $respuestasKobo, $nuevaEncuestaKobo2['id_kobo']);
                };
            }
           
        }*/
        //return $content
        //return $respuestasKobo;
    }
}