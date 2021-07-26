<?php

namespace App\Console\Commands;

use App\Models\Encuesta;
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
            
        } catch (\Throwable $e) {
            //throw $th;
            //return $e;
            return "Error en CRON!";
        }
        
    }
}
