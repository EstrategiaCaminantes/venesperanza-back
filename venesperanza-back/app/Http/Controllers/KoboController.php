<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Models\Encuesta;
use App\Models\MiembrosHogar;
use App\Models\Autorizacion;
use App\Models\Webhook;
use App\Models\NecesidadBasica;
use App\Models\Municipio;

use DateTime;

use Illuminate\Support\Facades\Storage;
use Guzzle\Http\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class KoboController extends Controller
{

    public function index()
    {
        
        $endpoint = env('KOBOENDPOINT');
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $endpoint, ['query' => [
            /*'fields' => '["Este_evento_tiene_incidencia_e_001", "_13_Hechos",
            "_geolocation", "today", "categoria", "group_nw4pj90/N_mero_de_desaparecidos",
            "group_nw4pj90/N_mero_de_fallecidos", "Riesgos"]'*/
        ],'auth' => [
            
            env('KOBOUSER'),
            
            env('KOBOPASSWORD')
        ]]);
        $statusCode = $response->getStatusCode();
        $formulariosKobo = json_decode($response->getBody(), true);
        $respuestasKobo = [];
        $totalkobo = 0;
        
        //foreach ($formulariosKobo as $encuestaKobo) {

                $encuesta_kobo_existe = Encuesta::where('fuente','=',3)->where('id_kobo','=',$formulariosKobo[99]['_id'])->first();

                if(!$encuesta_kobo_existe){
                    array_push( $respuestasKobo, $formulariosKobo[99]['_id']);
                    //cada kobo que no exista en la tabla encuesta de base datos, crea el registro nuevo con la info del kobo
                    //cada campo del kobo convertirlo al campo de la base datos
                    $nuevaEncuestaKobo = new Encuesta;

                    $nuevaEncuestaKobo->id_kobo = $formulariosKobo[99]['_id'];
                    $nuevaEncuestaKobo->fuente = 3;

                    if(isset($formulariosKobo[99]['Caracterizacion_GF/Fecha_llegada'])){
                        $nuevaEncuestaKobo->fecha_llegada_pais = $formulariosKobo[99]['Caracterizacion_GF/Fecha_llegada'];
                    }

                    if(isset($formulariosKobo[99]['Caracterizacion_GF/_En_qu_municipio_te_encuentras_ubicado'])){
                        $nuevaEncuestaKobo->ubicacion = $formulariosKobo[99]['Caracterizacion_GF/_En_qu_municipio_te_encuentras_ubicado'];
                    }

                    if(isset($formulariosKobo[99]['Caracterizacion_GF/_En_los_pr_ximos_seis_meses_pl'])){

                        if($formulariosKobo[99]['Caracterizacion_GF/_En_los_pr_ximos_seis_meses_pl'] === 's'){
                            $nuevaEncuestaKobo->estar_dentro_colombia = 1;
                        }else if($formulariosKobo[99]['Caracterizacion_GF/_En_los_pr_ximos_seis_meses_pl'] === 'no_estoy_seguro_a'){
                            $nuevaEncuestaKobo->estar_dentro_colombia = 2;
                        }else if($formulariosKobo[99]['Caracterizacion_GF/_En_los_pr_ximos_seis_meses_pl'] === 'no'){
                            $nuevaEncuestaKobo->estar_dentro_colombia = 0;
                        }
                    }

                    if(isset($formulariosKobo[99]['Caracterizacion_GF/_Cu_l_es_tu_destino_final_dent'])){

                        if($formulariosKobo[99]['Caracterizacion_GF/_Cu_l_es_tu_destino_final_dent'] === 'otro'){

                            $nuevaEncuestaKobo->nombre_municipio_destino_final = $formulariosKobo[99]['Caracterizacion_GF/_Cu_l'];

                        }else{

                            $municipioDestinoFinal = Municipio::where('nombre', '=', $formulariosKobo[99]['Caracterizacion_GF/_Cu_l_es_tu_destino_final_dent'])->first();

                            if($municipioDestinoFinal){
                                $nuevaEncuestaKobo->id_municipio_destino_final = $municipioDestinoFinal->id;
                            }else{
                                $nuevaEncuestaKobo->nombre_municipio_destino_final = $formulariosKobo[99]['Caracterizacion_GF/_Cu_l_es_tu_destino_final_dent'];

                            }
                        }
                        
                       
                    }

                    //return $nuevaEncuestaKobo;

                    $nuevaEncuestaKobo->save();

                    //Autorizacion
                    $autorizacion = new Autorizacion;

                    $autorizacion->id_encuesta = $nuevaEncuestaKobo->id;

                    if(isset($formulariosKobo[99]['Consentimiento/Autorizo_el_tratamiento_de_mis'])){

                        if($formulariosKobo[99]['Consentimiento/Autorizo_el_tratamiento_de_mis'] === 's'){
                            
                            $autorizacion->tratamiento_datos = 1;
                           
                        }else{
                            $autorizacion->tratamiento_datos = 0;
                        }
                        //return $kobo['Consentimiento/Autorizo_el_tratamiento_de_mis'];
                        $totalkobo += 1;
                    }

                    if(isset($formulariosKobo[99]['Consentimiento/Entiendo_y_acepto_lo_cipar_en_la_encuesta'])){

                        if($formulariosKobo[99]['Consentimiento/Entiendo_y_acepto_lo_cipar_en_la_encuesta'] === 's'){
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
        return $formulariosKobo[99];

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

    public function envioNotificacion(){
            try {
                //$messageBird = new \MessageBird\Client(env('MB_KEY')); // Set your own API access key here.  
                //$content = new \MessageBird\Objects\Conversation\Content();
                //$content->text = 'Hello world';
            
                $fechaActual = new DateTime();
                //$fechaActualModificada = $fechaActual->format('Y-m-d H:i:s');
                //$fechaActual->sub(new DateInterval('P3D'));
                 $fecha3diasAntes = date_sub($fechaActual, date_interval_create_from_date_string("3 days"));
                 $fecha3diasAntes0horas = $fecha3diasAntes->format('y-m-d 00:00:00'); //00:00:00 horas del dia
                 $fecha3diasAntes24horas = $fecha3diasAntes->format('y-m-d 23:59:59'); //23:59:59 horas del dia
                 //return $fecha3diasAntes24horas;
                 //return $fecha3diasAntes->format('Y-m-d H:i:s');
    
             
                //Consulta las encuentas creadas en los ultimos 3 dias, que no tienen reporte de llegada
                //fecha creacion mayor a hace 3 dias y ademas
                //que tienen 'linea_asociada_whataspp' = 1 (para web y kobo) o las que tienen 'waId' diferente de NULL
                $encuestas = Encuesta::doesnthave('llegadas')
                ->where('created_at','>=',$fecha3diasAntes0horas)
                ->where('created_at','<=',$fecha3diasAntes24horas)
                ->where(function($query){
                    $query->where('linea_asociada_whatsapp','=',1)->orWhere(function ($query2){
                        $query2->whereNotNull('waId')->where('pregunta','=',16);});
                })->get();


                //Cliente http
                $client = new \GuzzleHttp\Client();

                foreach($encuestas as $encuesta){
                
                    if($encuesta['waId']) {

                            $res = $client->request('POST', env('MB_ARRIVAL_REPORT'), 
                            [  
                                'form_params' => [
                                    'numero' => $encuesta['waId']
                                ]]);

                    }else if(strlen($encuesta['numero_contacto']) == 10 ){
                        $primerNumero = substr($encuesta['numero_contacto'],0,1);
                        
                            //validar si el numero es prefijo de operadores en venezuela o colombia para agregar alguno de los prefijos +58 o +57
                            if($primerNumero === '4'){

                                $numero_whatsapp = '+58'.$encuesta['numero_contacto'];

                                $res = $client->request('POST', env('MB_ARRIVAL_REPORT'), 
                                [  
                            
                                'form_params' => [
                                    'numero' => $numero_whatsapp
                                ]]);
                            

                            }else if($primerNumero === '3'){
                                
                                $numero_whatsapp = '+57'.$encuesta['numero_contacto'];
                                
                                $res = $client->request('POST', env('MB_ARRIVAL_REPORT'), 
                                [  
                            
                                'form_params' => [
                                    'numero' => $numero_whatsapp
                                ]]);
                                                            
                            }
                    }

                    
                }
                
                //return $encuestas;
                
                /*
                $mensajesAEnviar = [];

                foreach($encuestas as $encuesta){


                    //valido que alguno de los campos 'numero_contacto' o 'waId' empiece con +57 (colombia) o +58 (venezuela)
                    //y que sea de tamaño 10
                    if($encuesta['waId']) {

                        $datosMensaje = [
                            'id' => $encuesta['id'],
                            'primer_nombre' => $encuesta['primer_nombre'],
                            'primer_apellido' => $encuesta['primer_apellido'],
                            'numero_whatsapp' => $encuesta['waId']
                        ];

                        $mensajesAEnviar[] = $datosMensaje;

                    }else if(strlen($encuesta['numero_contacto']) == 10 ){

                        $primerNumero = substr($encuesta['numero_contacto'],0,1);
                        
                            //validar si el numero es prefijo de operadores en venezuela o colombia para agregar alguno de los prefijos +58 o +57
                            if($primerNumero === '4'){

                                $datosMensaje = [
                                    'id' => $encuesta['id'],
                                    'primer_nombre' => $encuesta['primer_nombre'],
                                    'primer_apellido' => $encuesta['primer_apellido'],
                                    'numero_whatsapp' => '+58'.$encuesta['numero_contacto']
                                ];
                            
                                $mensajesAEnviar[] = $datosMensaje;

                            }else if($primerNumero === '3'){
                                
                                $datosMensaje = [
                                    'id' => $encuesta['id'],
                                    'primer_nombre' => $encuesta['primer_nombre'],
                                    'primer_apellido' => $encuesta['primer_apellido'],
                                    'numero_whatsapp' => '+57'.$encuesta['numero_contacto']
                                ];
                                
                                $mensajesAEnviar[] = $datosMensaje;
                            }

                    }
                    
                }

                //return $mensajesAEnviar;

                
                if(sizeof($mensajesAEnviar) > 0){
                    try {
                        $res = $client->request('POST', env('URL_CHATBOT'), 
                            [   'headers' => ['Content-Type' => 'application/json',
                                                'auth' => [env('APP_API_KEY')],
                                ],
                            
                                'json' => $mensajesAEnviar]);

                        if($res->getstatusCode() == 200){
                            $respuesta = (string) $res->getBody();
                            $respuesta =json_decode($respuesta);
                            $mensaje = $respuesta->message;
                             
                             return $mensaje;
                            //'Mensajes mensaje!';
                         
                        }
                    
                    } catch (RequestException $exception) {
                        //$exception->getResponse()->getStatusCode()
                            return 'Error en Servidor ChatBot';
                        
                    }
                    
                }else{
                    return 'No hay mensajes para enviar!';
                }
                */
                
            } catch (\Throwable $e) {
                //throw $th;
                //return $e->getMessage();
                return "Error en CRON!";
            }
            
    }
}