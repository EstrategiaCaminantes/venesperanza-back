<?php

namespace App\Console\Commands;

use App\Models\Encuesta;
use App\Models\NotificacionLlegada;
use App\Models\ConversacionChat;

use Illuminate\Console\Command;

use Illuminate\Http\Request;

use Carbon\Carbon;
use DateTime;

use Guzzle\Http\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class NotificacionWhatsapp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notificacionwhatsapp:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envío de notificación a whatsapp';

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
    
                //Consulta las encuentas creadas menores a hace 3 dias, que no tienen reporte de llegada
                //que tienen 'linea_asociada_whataspp' = 1 (para web y kobo) o las que tienen 'waId' diferente de NULL y pregunta 16 es decir ya terminaron de responder
                //No tienen notificacion_llegada o Si tienen, pero reenviar==1 y updated_at menor a fecha de hace 3 dias


                //Anterior
                /*
                $encuestas = Encuesta::doesnthave('llegadas')
                //->where('created_at','>=',$fecha3diasAntes0horas)
                ->where('created_at','<=',$fecha3diasAntes24horas)
                ->where(function($query){
                    $query->where('linea_asociada_whatsapp','=',1)->orWhere(function ($query2){
                        $query2->whereNotNull('waId')->where('pregunta','=',16);});
                })->get();
                */

            
                $encuestas = Encuesta::doesnthave('llegadas')
                //->where('created_at','>=',$fecha3diasAntes0horas)
                ->where('created_at','<=',$fecha3diasAntes24horas)
                ->where(function($query){
                    $query->where('linea_asociada_whatsapp','=',1)->orWhere(function ($query2){
                        $query2->whereNotNull('waId')->where('pregunta','=',16);});
                })
				->where(function($query3){
                    $query3->doesnthave('notificacion_llegada')->orWhereHas('notificacion_llegada', function($q){
                        $fechaActual2 = new DateTime();
                        $fecha3diasAntes2 = date_sub($fechaActual2, date_interval_create_from_date_string("3 days"));
                        $fecha3diasAntes24horas2 = $fecha3diasAntes2->format('y-m-d 23:59:59'); //23:59:59 horas del dia

						$q->where('updated_at', '<=', $fecha3diasAntes24horas2)->where('reenviar','1');
						});
                })->get();

                
                //return $encuestas;

                //Cliente http
                $client = new \GuzzleHttp\Client();

                foreach($encuestas as $encuesta){

                    //Consulto con el waid o con el numero_contacto si ya existe el registro en la tabla 'notificacion_reporte_llegada'.
                    
                    if(strlen($encuesta['waId']) == 12 ) { //si en encuesta existe waid

                        $notificacion_reporte_llegada = NotificacionLlegada::where('waId','=',$encuesta['waId'])
                        ->where('numero_documento','=',$encuesta['numero_documento'])
                        ->where('tipo_documento','=',$encuesta['tipo_documento'])->first();
                        //return  $notificacion_reporte_llegada;
                        if(!$notificacion_reporte_llegada){ //si no existe registro

                            //Crea registro en 'notificacion_reporte_llegada... y envia notificacion

                            $nueva_notificacion_reporte_llegada = new NotificacionLlegada;

                            $nueva_notificacion_reporte_llegada->id_encuesta = $encuesta['id'];
                            $nueva_notificacion_reporte_llegada->waId = $encuesta['waId'];
                            $nueva_notificacion_reporte_llegada->numero_documento = $encuesta['numero_documento'];
                            $nueva_notificacion_reporte_llegada->tipo_documento = $encuesta['tipo_documento'];

                            if($nueva_notificacion_reporte_llegada->save()){
                                //Hace llamado a messagebird para enviar notificacion
                                
                                $res = $client->request('POST', env('MB_ARRIVAL_REPORT'), 
                                [  
                                    'form_params' => [
                                        'numero' => $encuesta['waId'],
                                        'nombre_contacto' => $encuesta['primer_nombre'].' '.$encuesta['primer_apellido']
                                    ]]);
                            }
                            

                        /*}else if ( $notificacion_reporte_llegada['reenviar'] == 1 && 
                        (($notificacion_reporte_llegada['created_at'] <= $fecha3diasAntes24horas && !$notificacion_reporte_llegada['updated_at']) ||
                        ($notificacion_reporte_llegada['updated_at'] <= $fecha3diasAntes24horas)) ){*/
                        }else {
                            $notificacion_reporte_llegada->reenviar = 0;
                            
                            if($notificacion_reporte_llegada->save()){
                                
                                $res = $client->request('POST', env('MB_ARRIVAL_REPORT'), 
                                [  
                                'form_params' => [
                                    'numero' => $encuesta['waId'],
                                    'nombre_contacto' => $encuesta['primer_nombre'].' '.$encuesta['primer_apellido']
                                ]]);
                            }

                                
                        }

                            

                    }else if(strlen($encuesta['numero_contacto']) == 10 ){ //En Encuesta no hay waId pero si hay numero_contacto
                        
                        
                        $primerNumero = substr($encuesta['numero_contacto'],0,1);
                        
                            //validar si el numero es prefijo de operadores en venezuela o colombia para agregar alguno de los prefijos +58 o +57
                            if($primerNumero === '4'){

                                $numero_whatsapp = '58'.$encuesta['numero_contacto'];

                                $notificacion_reporte_llegada = NotificacionLlegada::where('waId','=',$numero_whatsapp )
                                ->where('numero_documento','=',$encuesta['numero_documento'])
                                ->where('tipo_documento','=',$encuesta['tipo_documento'])->first();
                        
                                if(!$notificacion_reporte_llegada){ //si no existe registro

                                    //Crea registro en 'notificacion_reporte_llegada

                                    $nueva_notificacion_reporte_llegada = new NotificacionLlegada;

                                    $nueva_notificacion_reporte_llegada->id_encuesta = $encuesta['id'];
                                    $nueva_notificacion_reporte_llegada->waId = $numero_whatsapp;
                                    $nueva_notificacion_reporte_llegada->numero_documento = $encuesta['numero_documento'];
                                    $nueva_notificacion_reporte_llegada->tipo_documento = $encuesta['tipo_documento'];

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
                                            }
                                            
                                        }else{

                                             //return 'CNVERSA YA EXISTE';
                                             $conversacion->autorizacion = 1;
                                             if($conversacion->save()){
                                                //si conversacion ya existe envia la notificacion
                                                
                                                $res = $client->request('POST', env('MB_ARRIVAL_REPORT'), 
                                                [  
                                                    'form_params' => [
                                                        'numero' => $numero_whatsapp,
                                                        'nombre_contacto' => $encuesta['primer_nombre'].' '.$encuesta['primer_apellido']
                                                    ]]);
                                            }
                                            
                                        }
                                    }
                                    

                                }else {
                                    //existe y reenviar == 1, valida que haya pasado 3 dias en fecha de creacion y actualizacion sea nulo, o, hayan pasado 3 dias en fecha de actualizacion
                                    //la conversacion ya existe, se creo cuando se envio la primera notificacion de reporte de llegada
                                        $notificacion_reporte_llegada->reenviar = 0;

                                        if($notificacion_reporte_llegada->save()){
                                            
                                            $res = $client->request('POST', env('MB_ARRIVAL_REPORT'), 
                                            [  
                                            'form_params' => [
                                                'numero' => $numero_whatsapp,
                                                'nombre_contacto' => $encuesta['primer_nombre'].' '.$encuesta['primer_apellido']
                                            ]]);
                                        }
                                }

                                

                            }else if($primerNumero === '3'){
                                
                                $numero_whatsapp = '57'.$encuesta['numero_contacto'];
                                
                                $notificacion_reporte_llegada = NotificacionLlegada::where('waId','=',$numero_whatsapp )
                                ->where('numero_documento','=',$encuesta['numero_documento'])
                                ->where('tipo_documento','=',$encuesta['tipo_documento'])->first();
                        
                                if(!$notificacion_reporte_llegada){ //si no existe registro

                                    //Crea registro en 'notificacion_reporte_llegada... y envia notificacion

                                    $nueva_notificacion_reporte_llegada = new NotificacionLlegada;

                                    $nueva_notificacion_reporte_llegada->id_encuesta = $encuesta['id'];
                                    $nueva_notificacion_reporte_llegada->waId = $numero_whatsapp;
                                    $nueva_notificacion_reporte_llegada->numero_documento = $encuesta['numero_documento'];
                                    $nueva_notificacion_reporte_llegada->tipo_documento = $encuesta['tipo_documento'];

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
                                            }
                                            
                                        }else{

                                            //return 'CNVERSA YA EXISTE';
                                            $conversacion->autorizacion = 1;

                                            if($conversacion->save()){
                                                //si conversacion ya existe envia la notificacion
                                                
                                                $res = $client->request('POST', env('MB_ARRIVAL_REPORT'), 
                                                [  
                                                    'form_params' => [
                                                        'numero' => $numero_whatsapp,
                                                        'nombre_contacto' => $encuesta['primer_nombre'].' '.$encuesta['primer_apellido']
                                                    ]]);
                                            }
                                            
                                        }
                                        
                                    }
                                    

                                }else {
                                    //existe y reenviar == 1, valida que haya pasado 3 dias en fecha de creacion y actualizacion sea nulo, o, hayan pasado 3 dias en fecha de actualizacion
                                    //envia notificacion a una conversacion que ya existe
                                        $notificacion_reporte_llegada->reenviar = 0;

                                        if($notificacion_reporte_llegada->save()){
                                            
                                            $res = $client->request('POST', env('MB_ARRIVAL_REPORT'), 
                                            [  
                                            'form_params' => [
                                                'numero' => $numero_whatsapp,
                                                'nombre_contacto' => $encuesta['primer_nombre'].' '.$encuesta['primer_apellido']
                                            ]]);
                                        }
                                        
                                }
                                                            
                            }
                    }

                    
                }
            
            
        } catch (\Throwable $e) {
            //throw $th;
            //return $e;
            return "Error en CRON!";
        }

        /*
        //Funcion para actualizar 'numero_documento' y 'tipo_documento' de registros d enotificaciones
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
         */
        
    }
}
