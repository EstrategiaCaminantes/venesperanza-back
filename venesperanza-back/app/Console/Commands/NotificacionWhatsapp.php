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
            

            //base_uri es la url para llamar a la funcion /notificacionwhatsapp de app.j (chatbot)
            //la url se define en .env variable URL_CHATBOT 
            //ejemplo dev: https://fe2980fe2c5b.ngrok.io/notificacionwhatsapp

            $client = new GuzzleHttp\Client(['base_uri' => env('URL_CHATBOT')]);

            //Consulta las encuestas que tienen 'linea_asociada_whataspp' = 1 (para web y kobo) o las que tienen 'waId' diferente de NULL
            $encuestas = Encuesta::where('linea_asociada_whatsapp','=',1)->orWhere(function($query) {
                $query->whereNotNull('waId');
            })->get();
                
            

            foreach($encuestas as $encuesta){

                //valido que alguno de los campos 'numero_contacto' o 'waId' empiece con +57 (colombia) o +58 (venezuela)
                //y que sea de tamaño 10
                if(sizeof($encuesta['numero_contacto']) == 10 || sizeof($encuesta['numero_contacto']) == 11){

                    if($encuesta['numero_contacto'].substring(0,3) === '+57' || $encuesta['numero_contacto'].substring(0,3) === '+58'){

                        //hago llamado a url del back chatbot con los datos de cada encuesta
                        $response = $client->post(env('URL_CHATBOT'),[
                            // un array con la data de los headers como tipo de peticion, etc.
                            'headers' => ['foo' => 'bar'],
                            // array de datos del formulario
                            'body' => [
                                'primer_nombre' => $encuesta['primer_nombre'],
                                'primer_apellido' => $encuesta['primer_apellido'],
                                'numero_whatsapp' => $encuesta['numero_contacto']
                            ]
                        ]);
                    }


                }else if( sizeof($encuesta['waId']) == 10 || sizeof($encuesta['waId']) == 11){

                    if($encuesta['waId'].substring(0,3) === '+57' || $encuesta['waId'].substring(0,3) === '+58'){

                            //hago llamado a url del back chatbot con los datos de cada encuesta
                            $response = $client->post(env('URL_CHATBOT'),[
                                // un array con la data de los headers como tipo de peticion, etc.
                                'headers' => ['foo' => 'bar'],
                                // array de datos del formulario
                                'body' => [
                                    'primer_nombre' => $encuesta['primer_nombre'],
                                    'primer_apellido' => $encuesta['primer_apellido'],
                                    'numero_whatsapp' => $encuesta['waId']
                                ]
                            ]);

                    }

                }
                
            }


            


        } catch (\Throwable $e) {
            //throw $th;
        }
        
    }
}
