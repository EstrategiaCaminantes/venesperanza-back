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
use App\Models\NotificacionLlegada;
use App\Models\ConversacionChat;
use App\Models\LogsMensajesAuto;
use App\Models\Llegadas;
use App\Models\DatosActualizados;


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
        
             
            $fechaActual = new DateTime();
            //$fechaActualModificada = $fechaActual->format('Y-m-d H:i:s');
            //$fechaActual->sub(new DateInterval('P3D'));
             $fecha3diasAntes = date_sub($fechaActual, date_interval_create_from_date_string("3 days"));
             $fecha3diasAntes0horas = $fecha3diasAntes->format('y-m-d 00:00:00'); //00:00:00 horas del dia
             $fecha3diasAntes24horas = $fecha3diasAntes->format('y-m-d 23:59:59'); //23:59:59 horas del dia
            
            
            $encuestas = Encuesta::doesnthave('llegadas')
            //->where('created_at','>=',$fecha3diasAntes0horas)
            ->where('created_at','<=',$fecha3diasAntes24horas)->where('numero_contacto','=','3175049604')
            ->where(function($query){
                $query->where('linea_asociada_whatsapp','=',1)->orWhere(function ($query2){
                    $query2->whereNotNull('waId')->where('pregunta','=',19);});
            })
            ->where(function($query3){
                $query3->doesnthave('notificacion_llegada')->orWhereHas('notificacion_llegada', function($q){
                    $fechaActual2 = new DateTime();
                    $fecha3diasAntes2 = date_sub($fechaActual2, date_interval_create_from_date_string("3 days"));
                    $fecha3diasAntes24horas2 = $fecha3diasAntes2->format('y-m-d 23:59:59'); //23:59:59 horas del dia

                    $q->where('updated_at', '<=', $fecha3diasAntes24horas2)
                    //->where('reenviar','1')
                    ->where(function ($queryreenviar) {
                       $queryreenviar->where('reenviar', '=', 1)
                           ->orWhereNull('respuesta');
                       })
                    ->where('activo','1');
                    });
            })->get();

            
           //return $encuestas;

            //Cliente http
            $client = new \GuzzleHttp\Client();

            foreach($encuestas as $encuesta){

                //Consulto con el waid o con el numero_contacto si ya existe el registro en la tabla 'notificacion_reporte_llegada'.
                
                if(strlen($encuesta['waId']) == 12 ) { //si en encuesta existe waid

                    $notificacion_reporte_llegada = NotificacionLlegada::where('waId','=',$encuesta['waId'])
                    ->where('activo','1')->first();
                    //return  $notificacion_reporte_llegada;
                    if(!$notificacion_reporte_llegada){ //si no existe registro

                        //Crea registro en 'notificacion_reporte_llegada... y envia notificacion

                        $nueva_notificacion_reporte_llegada = new NotificacionLlegada;

                        $nueva_notificacion_reporte_llegada->id_encuesta = $encuesta['id'];
                        $nueva_notificacion_reporte_llegada->waId = $encuesta['waId'];
                        $nueva_notificacion_reporte_llegada->activo = 1;

                        if($nueva_notificacion_reporte_llegada->save()){
                            //Hace llamado a messagebird para enviar notificacion

                            
                            $res = $client->request('POST', env('MB_ARRIVAL_REPORT'), 
                            [  
                                'form_params' => [
                                    'numero' => $encuesta['waId'],
                                    'nombre_contacto' => $encuesta['primer_nombre'].' '.$encuesta['primer_apellido']
                                ]]);
                            
                            $statusCodeLlamadoAMessageBird = $res->getStatusCode();
                            
                            if(strval($statusCodeLlamadoAMessageBird)[0] === '2'){

                                //return $encuesta;
                                $logMensaje = new LogsMensajesAuto;
                                $logMensaje->waId = $encuesta['waId'];
                                $logMensaje->mensaje = 'reporte_llegada';
                                $logMensaje->tipo_mensaje = 1;
                                $logMensaje->save();

                                $actualizoConversacion = ConversacionChat::where('waId','=',$encuesta['waId'])->first();
                                $actualizoConversacion->updated_at = new DateTime();
                                $actualizoConversacion->save();

                            }
                             
                        }

                    /*}else if ( $notificacion_reporte_llegada['reenviar'] == 1 && 
                    (($notificacion_reporte_llegada['created_at'] <= $fecha3diasAntes24horas && !$notificacion_reporte_llegada['updated_at']) ||
                    ($notificacion_reporte_llegada['updated_at'] <= $fecha3diasAntes24horas)) ){*/
                    }else if($notificacion_reporte_llegada['id_encuesta'] == $encuesta['id']){
                        
                        $notificacion_reporte_llegada->reenviar = 0;
                        $notificacion_reporte_llegada->updated_at = new DateTime();
                        
                        if($notificacion_reporte_llegada->save()){
                            
                            
                            $res = $client->request('POST', env('MB_ARRIVAL_REPORT'), 
                            [  
                            'form_params' => [
                                'numero' => $encuesta['waId'],
                                'nombre_contacto' => $encuesta['primer_nombre'].' '.$encuesta['primer_apellido']
                            ]]);

                            $statusCodeLlamadoAMessageBird = $res->getStatusCode();
                            
                            if(strval($statusCodeLlamadoAMessageBird)[0] === '2'){

                                //return $encuesta;
                                $logMensaje = new LogsMensajesAuto;
                                $logMensaje->waId = $encuesta['waId'];
                                $logMensaje->mensaje = 'reporte_llegada';
                                $logMensaje->tipo_mensaje = 1;
                                $logMensaje->save();

                                $actualizoConversacion = ConversacionChat::where('waId','=',$encuesta['waId'])->first();
                                $actualizoConversacion->updated_at = new DateTime();
                                $actualizoConversacion->save();
                            }
                        }

                            
                    }else{
                        $nueva_notificacion_reporte_llegada = new NotificacionLlegada;

                        $nueva_notificacion_reporte_llegada->id_encuesta = $encuesta['id'];
                        $nueva_notificacion_reporte_llegada->waId = $encuesta['waId'];
                        $nueva_notificacion_reporte_llegada->activo = 0;
                        $nueva_notificacion_reporte_llegada->save();
                    }

                        

                }else if(strlen($encuesta['numero_contacto']) == 10 ){ //En Encuesta no hay waId pero si hay numero_contacto
                    
                    
                    $primerNumero = substr($encuesta['numero_contacto'],0,1);
                    
                        //validar si el numero es prefijo de operadores en venezuela o colombia para agregar alguno de los prefijos +58 o +57
                        if($primerNumero === '4'){

                            $numero_whatsapp = '58'.$encuesta['numero_contacto'];

                            $notificacion_reporte_llegada = NotificacionLlegada::where('waId','=',$numero_whatsapp )
                            ->where('activo','=','1')->first();
                    
                            if(!$notificacion_reporte_llegada){ //si no existe registro

                                //Crea registro en 'notificacion_reporte_llegada

                                $nueva_notificacion_reporte_llegada = new NotificacionLlegada;

                                $nueva_notificacion_reporte_llegada->id_encuesta = $encuesta['id'];
                                $nueva_notificacion_reporte_llegada->waId = $numero_whatsapp;
                                $nueva_notificacion_reporte_llegada->activo = 1;

                                if($nueva_notificacion_reporte_llegada->save()){
                                    
                                    //consula si existe conversacion-chatbot con el numero_contacto
                                    $conversacion = ConversacionChat::where('waId','=',$numero_whatsapp)->first();

                                    if(!$conversacion){
                                        //si no existe crea la conversacion con autorizacion 1 porque ya autorizo tratamiento de datos para venesperanza
                                        
                                        $nuevaConversacion = new ConversacionChat;

                                        $nuevaConversacion->conversation_start = 1;
                                        $nuevaConversacion->waId = $numero_whatsapp;
                                        $nuevaConversacion->profileName = $encuesta['primer_nombre'];
                                        $nuevaConversacion->autorizacion = 1;

                                        if($nuevaConversacion->save()){
                                            //Hace llamado a messagebird para enviar notificacion
                                            
                                            
                                            $res = $client->request('POST', env('MB_ARRIVAL_REPORT'), 
                                            [  
                                                'form_params' => [
                                                    'numero' => $numero_whatsapp,
                                                    'nombre_contacto' => $encuesta['primer_nombre'].' '.$encuesta['primer_apellido']
                                                ]]);

                                            $statusCodeLlamadoAMessageBird = $res->getStatusCode();
                            
                                            if(strval($statusCodeLlamadoAMessageBird)[0] === '2'){
                    
                                                    //return $encuesta;
                                                $logMensaje = new LogsMensajesAuto;
                                                $logMensaje->waId = $numero_whatsapp;
                                                $logMensaje->mensaje = 'reporte_llegada';
                                                $logMensaje->tipo_mensaje = 1;
                                                $logMensaje->save();
                                            }
                                                
                                        }
                                        
                                    }else{

                                         //return 'CNVERSA YA EXISTE';
                                         $conversacion->autorizacion = 1;
                                         $conversacion->updated_at = new DateTime();
                                         
                                         if($conversacion->save()){
                                            //si conversacion ya existe envia la notificacion
                                            
                                            
                                            $res = $client->request('POST', env('MB_ARRIVAL_REPORT'), 
                                            [  
                                                'form_params' => [
                                                    'numero' => $numero_whatsapp,
                                                    'nombre_contacto' => $encuesta['primer_nombre'].' '.$encuesta['primer_apellido']
                                                ]]);
                                            
                                            $statusCodeLlamadoAMessageBird = $res->getStatusCode();
                            
                                            if(strval($statusCodeLlamadoAMessageBird)[0] === '2'){
                        
                                                //return $encuesta;
                                                $logMensaje = new LogsMensajesAuto;
                                                $logMensaje->waId = $numero_whatsapp;
                                                $logMensaje->mensaje = 'reporte_llegada';
                                                $logMensaje->tipo_mensaje = 1;
                                                $logMensaje->save();
                                            }
                                        }
                                        
                                    }
                                }
                                

                            }else if($notificacion_reporte_llegada['id_encuesta'] == $encuesta['id']){
                                //existe y reenviar == 1, valida que haya pasado 3 dias en fecha de creacion y actualizacion sea nulo, o, hayan pasado 3 dias en fecha de actualizacion
                                //la conversacion ya existe, se creo cuando se envio la primera notificacion de reporte de llegada
                                    $notificacion_reporte_llegada->reenviar = 0;
                                    $notificacion_reporte_llegada->updated_at = new DateTime();

                                    if($notificacion_reporte_llegada->save()){
                                        
                                        $res = $client->request('POST', env('MB_ARRIVAL_REPORT'), 
                                        [  
                                        'form_params' => [
                                            'numero' => $numero_whatsapp,
                                            'nombre_contacto' => $encuesta['primer_nombre'].' '.$encuesta['primer_apellido']
                                        ]]);

                                        $statusCodeLlamadoAMessageBird = $res->getStatusCode();
                            
                                        if(strval($statusCodeLlamadoAMessageBird)[0] === '2'){
                        
                                                //return $encuesta;
                                            $logMensaje = new LogsMensajesAuto;
                                            $logMensaje->waId = $numero_whatsapp;
                                            $logMensaje->mensaje = 'reporte_llegada';
                                            $logMensaje->tipo_mensaje = 1;
                                            $logMensaje->save();
                                        }

                                        
                                    }
                            }else{

                                $nueva_notificacion_reporte_llegada = new NotificacionLlegada;

                                $nueva_notificacion_reporte_llegada->id_encuesta = $encuesta['id'];
                                $nueva_notificacion_reporte_llegada->waId = $numero_whatsapp;
                                $nueva_notificacion_reporte_llegada->activo = 0;
                                $nueva_notificacion_reporte_llegada->save();
                            }

                            

                        }else if($primerNumero === '3'){
                            
                            $numero_whatsapp = '57'.$encuesta['numero_contacto'];
                            
                            $notificacion_reporte_llegada = NotificacionLlegada::where('waId','=',$numero_whatsapp )
                            ->where('activo','=','1')->first();
                    
                            if(!$notificacion_reporte_llegada){ //si no existe registro

                                
                                //Crea registro en 'notificacion_reporte_llegada... y envia notificacion

                                $nueva_notificacion_reporte_llegada = new NotificacionLlegada;

                                $nueva_notificacion_reporte_llegada->id_encuesta = $encuesta['id'];
                                $nueva_notificacion_reporte_llegada->waId = $numero_whatsapp;
                                $nueva_notificacion_reporte_llegada->activo = 1;

                                if($nueva_notificacion_reporte_llegada->save()){

                                    //Consulta si existe una conversacion con el numero_contacto
                                    $conversacion = ConversacionChat::where('waId','=',$numero_whatsapp)->first();
                                    
                                    
                                    if(!$conversacion){
                                       
                                        
                                        //no existe conversacion entonces la crea con autorizacion = 1 y envia notificacion
                                        $nuevaConversacion = new ConversacionChat;

                                        $nuevaConversacion->conversation_start = 1;
                                        $nuevaConversacion->waId = $numero_whatsapp;
                                        $nuevaConversacion->profileName = $encuesta['primer_nombre'];
                                        $nuevaConversacion->autorizacion = 1;
                                        
                                        
                                        if($nuevaConversacion->save()){

                                            
                                            //Hace llamado a messagebird para enviar notificacion
                                            
                                            $res = $client->request('POST', env('MB_ARRIVAL_REPORT'), 
                                            [  
                                                'form_params' => [
                                                    'numero' => $numero_whatsapp,
                                                    'nombre_contacto' => $encuesta['primer_nombre'].' '.$encuesta['primer_apellido']
                                                ]]);

                                            $statusCodeLlamadoAMessageBird = $res->getStatusCode();
                            
                                            if(strval($statusCodeLlamadoAMessageBird)[0] === '2'){
                                
                                                //return $encuesta;
                                                $logMensaje = new LogsMensajesAuto;
                                                $logMensaje->waId = $numero_whatsapp;
                                                $logMensaje->mensaje = 'reporte_llegada';
                                                $logMensaje->tipo_mensaje = 1;
                                                $logMensaje->save();
                                            }
                                        }
                                        
                                    }else{

                                        //return 'CNVERSA YA EXISTE';
                                        $conversacion->autorizacion = 1;
                                        $conversacion->updated_at = new DateTime();

                                        if($conversacion->save()){
                                            //si conversacion ya existe envia la notificacion
                                            
                                            $res = $client->request('POST', env('MB_ARRIVAL_REPORT'), 
                                            [  
                                                'form_params' => [
                                                    'numero' => $numero_whatsapp,
                                                    'nombre_contacto' => $encuesta['primer_nombre'].' '.$encuesta['primer_apellido']
                                                ]]);
                                            
                                            $statusCodeLlamadoAMessageBird = $res->getStatusCode();
                            
                                            if(strval($statusCodeLlamadoAMessageBird)[0] === '2'){
                                
                                                    //return $encuesta;
                                                $logMensaje = new LogsMensajesAuto;
                                                $logMensaje->waId = $numero_whatsapp;
                                                $logMensaje->mensaje = 'reporte_llegada';
                                                $logMensaje->tipo_mensaje = 1;
                                                $logMensaje->save();
                                            }
                                        }
                                        
                                    }
                                    
                                }
                                

                            }else if($notificacion_reporte_llegada['id_encuesta'] == $encuesta['id']){
                                //existe y reenviar == 1, valida que haya pasado 3 dias en fecha de creacion y actualizacion sea nulo, o, hayan pasado 3 dias en fecha de actualizacion
                                //envia notificacion a una conversacion que ya existe
                                    $notificacion_reporte_llegada->reenviar = 0;
                                    $notificacion_reporte_llegada->updated_at = new DateTime();

                                    if($notificacion_reporte_llegada->save()){
                                        
                                        $res = $client->request('POST', env('MB_ARRIVAL_REPORT'), 
                                        [  
                                        'form_params' => [
                                            'numero' => $numero_whatsapp,
                                            'nombre_contacto' => $encuesta['primer_nombre'].' '.$encuesta['primer_apellido']
                                        ]]);

                                        $statusCodeLlamadoAMessageBird = $res->getStatusCode();
                            
                                        if(strval($statusCodeLlamadoAMessageBird)[0] === '2'){
                        
                                                //return $encuesta;
                                            $logMensaje = new LogsMensajesAuto;
                                            $logMensaje->waId = $numero_whatsapp;
                                            $logMensaje->mensaje = 'reporte_llegada';
                                            $logMensaje->tipo_mensaje = 1;
                                            $logMensaje->save();
                                        }
                                    }
                                    
                            }else{
                                $nueva_notificacion_reporte_llegada = new NotificacionLlegada;

                                $nueva_notificacion_reporte_llegada->id_encuesta = $encuesta['id'];
                                $nueva_notificacion_reporte_llegada->waId = $numero_whatsapp;
                                $nueva_notificacion_reporte_llegada->activo = 0;
                                $nueva_notificacion_reporte_llegada->save();
                            }
                                                        
                        }
                }else if(strlen($encuesta['numero_contacto']) == 12  ){

                    $primerosDosNumeros = substr($encuesta['numero_contacto'],0,2);

                    if($primerosDosNumeros === '58' || $primerosDosNumeros === '57' ){
                        //return $encuesta;
                        $notificacion_reporte_llegada = NotificacionLlegada::where('waId','=',$encuesta['numero_contacto'] )
                            ->where('activo','=','1')->first();
                    
                            if(!$notificacion_reporte_llegada){ //si no existe registro

                                //Crea registro en 'notificacion_reporte_llegada... y envia notificacion

                                $nueva_notificacion_reporte_llegada = new NotificacionLlegada;

                                $nueva_notificacion_reporte_llegada->id_encuesta = $encuesta['id'];
                                $nueva_notificacion_reporte_llegada->waId = $encuesta['numero_contacto'];
                                $nueva_notificacion_reporte_llegada->activo = 1;

                                if($nueva_notificacion_reporte_llegada->save()){

                                    //Consulta si existe una conversacion con el numero_contacto
                                    $conversacion = ConversacionChat::where('waId','=',$encuesta['numero_contacto'])->first();
                                    
                                    
                                    if(!$conversacion){

                                        
                                        //no existe conversacion entonces la crea con autorizacion = 1 y envia notificacion
                                        $nuevaConversacion = new ConversacionChat;

                                        $nuevaConversacion->conversation_start = 1;
                                        $nuevaConversacion->waId = $encuesta['numero_contacto'];
                                        $nuevaConversacion->profileName = $encuesta['primer_nombre'];
                                        $nuevaConversacion->autorizacion = 1;
                                        
                                        
                                        if($nuevaConversacion->save()){

                                            
                                            //Hace llamado a messagebird para enviar notificacion
                                            
                                            $res = $client->request('POST', env('MB_ARRIVAL_REPORT'), 
                                            [  
                                                'form_params' => [
                                                    'numero' => $numero_whatsapp,
                                                    'nombre_contacto' => $encuesta['primer_nombre'].' '.$encuesta['primer_apellido']
                                                ]]);
                                            
                                            $statusCodeLlamadoAMessageBird = $res->getStatusCode();
                            
                                            if(strval($statusCodeLlamadoAMessageBird)[0] === '2'){
                                
                                                //return $encuesta;
                                                $logMensaje = new LogsMensajesAuto;
                                                $logMensaje->waId = $numero_whatsapp;
                                                $logMensaje->mensaje = 'reporte_llegada';
                                                $logMensaje->tipo_mensaje = 1;
                                                $logMensaje->save();
                                            }



                                        }
                                        
                                    }else{

                                        //return 'CNVERSA YA EXISTE';
                                        $conversacion->autorizacion = 1;
                                        $conversacion->updated_at = new DateTime();

                                        if($conversacion->save()){
                                            //si conversacion ya existe envia la notificacion
                                            
                                            $res = $client->request('POST', env('MB_ARRIVAL_REPORT'), 
                                            [  
                                                'form_params' => [
                                                    'numero' => $numero_whatsapp,
                                                    'nombre_contacto' => $encuesta['primer_nombre'].' '.$encuesta['primer_apellido']
                                                ]]);
                                            
                                            $statusCodeLlamadoAMessageBird = $res->getStatusCode();
                            
                                            if(strval($statusCodeLlamadoAMessageBird)[0] === '2'){
                                
                                                //return $encuesta;
                                                $logMensaje = new LogsMensajesAuto;
                                                $logMensaje->waId = $numero_whatsapp;
                                                $logMensaje->mensaje = 'reporte_llegada';
                                                $logMensaje->tipo_mensaje = 1;
                                                $logMensaje->save();
                                            }
                                        }
                                        
                                    }
                                    
                                }
                                

                            }else if($notificacion_reporte_llegada['id_encuesta'] == $encuesta['id']){
                                //existe y reenviar == 1, valida que haya pasado 3 dias en fecha de creacion y actualizacion sea nulo, o, hayan pasado 3 dias en fecha de actualizacion
                                //envia notificacion a una conversacion que ya existe
                                    $notificacion_reporte_llegada->reenviar = 0;
                                    $notificacion_reporte_llegada->updated_at = new DateTime();

                                    if($notificacion_reporte_llegada->save()){
                                        
                                        $res = $client->request('POST', env('MB_ARRIVAL_REPORT'), 
                                        [  
                                        'form_params' => [
                                            'numero' => $numero_whatsapp,
                                            'nombre_contacto' => $encuesta['primer_nombre'].' '.$encuesta['primer_apellido']
                                        ]]);

                                        $statusCodeLlamadoAMessageBird = $res->getStatusCode();
                            
                                        if(strval($statusCodeLlamadoAMessageBird)[0] === '2'){
                        
                                                //return $encuesta;
                                            $logMensaje = new LogsMensajesAuto;
                                            $logMensaje->waId = $numero_whatsapp;
                                            $logMensaje->mensaje = 'reporte_llegada';
                                            $logMensaje->tipo_mensaje = 1;
                                            $logMensaje->save();
                                        }

                                    }
                                    
                            }else{
                                $nueva_notificacion_reporte_llegada = new NotificacionLlegada;

                                $nueva_notificacion_reporte_llegada->id_encuesta = $encuesta['id'];
                                $nueva_notificacion_reporte_llegada->waId = $encuesta['numero_contacto'];
                                $nueva_notificacion_reporte_llegada->activo = 0;
                                $nueva_notificacion_reporte_llegada->save();
                            }
                    }



                }

                
            }
        
    } catch (\Throwable $e) {
        //throw $th;
        return $e;
        return "Error en CRON!";
    }
            
    }

    public function actualicarDatosNotificaciones(){

        try {
            $llegadas = NotificacionLlegada::whereNull('tipo_documento')->get();

            //return $llegadas->count();
            foreach($llegadas as $llegada){

                $encuesta = Encuesta::where('id','=',$llegada['id_encuesta'])->first();

                if($encuesta){
                    $llegada->numero_documento = $encuesta['numero_documento'];
                    $llegada->tipo_documento = $encuesta['tipo_documento'];

                    $llegada->save();
                }
            }

        } catch (\Throwable $e) {
            return $e;
        }
    }


    //Funcion para actualizar Llegadas y datos_actualizados que tienen id_encuesta=NULL
    public function actualizarLlegadasYDatosActualizadosSinIdEncuesta(){

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