<?php

namespace App\Console\Commands;

use App\Models\Encuesta;
use Illuminate\Console\Command;

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
                
            $fechaActual = new DateTime();
            //$fechaActualModificada = $fechaActual->format('Y-m-d H:i:s');
            //$fechaActual->sub(new DateInterval('P3D'));
             $fecha3diasAntes = date_sub($fechaActual, date_interval_create_from_date_string("3 days"));
             $fecha3diasAntes0horas = $fecha3diasAntes->format('y-m-d 00:00:00'); //00:00:00 horas del dia
             $fecha3diasAntes24horas = $fecha3diasAntes->format('y-m-d 23:59:59'); //23:59:59 horas del dia
             //return $fecha3diasAntes24horas;
             //return $fecha3diasAntes->format('Y-m-d H:i:s');
      

            
            //base_uri es la url para llamar a la funcion /notificacionwhatsapp de app.j (chatbot)
            //la url se define en .env variable URL_CHATBOT 
            //ejemplo dev: https://fe2980fe2c5b.ngrok.io/notificacionwhatsapp

            //$client = new GuzzleHttp\Client(['base_uri' => env('URL_CHATBOT')]);
            $client = new \GuzzleHttp\Client();
           
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
            
            //return $encuestas;
                
            $mensajesAEnviar = [];

            foreach($encuestas as $encuesta){

                if($encuesta)

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

                }else if(strlen($encuesta['numero_contacto']) == 10 /*|| sizeof($encuesta['numero_contacto']) == 11*/){

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
            
        } catch (\Throwable $e) {
            //throw $th;
            return $e;
            return "Error en CRON!";
        }
        
    }
}
