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
        foreach ($formulariosKobo as $kobo) {

            $respuesta_kobo_existe = Encuesta::where('fuente','=','3')->where('id_kobo','=',$kobo['_id'])->first();

            if(!$respuesta_kobo_existe){
                array_push( $respuestasKobo, $kobo['_id']);
                //cada kobo que no exista en la tabla encuesta de base datos, crea el registro nuevo con la info del kobo
                //cada campo del kobo convertirlo al campo de la base datos
            }
        }
        //return $content
        return count($respuestasKobo);
    }
}