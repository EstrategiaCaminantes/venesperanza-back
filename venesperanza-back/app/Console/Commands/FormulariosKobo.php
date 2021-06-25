<?php

namespace App\Console\Commands;

use App\Models\Autorizacion;
use App\Models\Encuesta;
use Illuminate\Console\Command;

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
        ], 'auth' => [
            //'snavarrete',
            env('KOBOUSER'),
            //'toxicity.1'
            env('KOBOPASSWORD'),
        ]]);
        $statusCode = $response->getStatusCode();
        $formulariosKobo = json_decode($response->getBody(), true);

        $respuestasKobo = [];
        $totalkobo = 0;

        foreach ($formulariosKobo as $encuestaKobo) {

            $encuesta_kobo_existe = Encuesta::where('fuente', '=', 3)->where('id_kobo', '=', $encuestaKobo['_id'])->first();

            if (!$encuesta_kobo_existe) {
                array_push($respuestasKobo, $encuestaKobo['_id']);
                //cada kobo que no exista en la tabla encuesta de base datos, crea el registro nuevo con la info del kobo
                //cada campo del kobo convertirlo al campo de la base datos
                $nuevaEncuestaKobo = new Encuesta;

                $nuevaEncuestaKobo->id_kobo = $encuestaKobo['_id'];
                $nuevaEncuestaKobo->fuente = 3;

                if (isset($encuestaKobo['Cabeza_de_Hogar/C_mo_es_su_primer_nombre'])) {
                    $nuevaEncuestaKobo->primer_nombre = $encuestaKobo['Cabeza_de_Hogar/C_mo_es_su_primer_nombre'];
                }
                if (isset($encuestaKobo['Cabeza_de_Hogar/primer_nombre'])) {
                    $nuevaEncuestaKobo->primer_nombre = $encuestaKobo['Cabeza_de_Hogar/primer_nombre'];
                }

                if (isset($encuestaKobo['Cabeza_de_Hogar/C_mo_es_su_segundo_nombre'])) {
                    $nuevaEncuestaKobo->segundo_nombre = $encuestaKobo['Cabeza_de_Hogar/C_mo_es_su_segundo_nombre'];
                }
                if (isset($encuestaKobo['Cabeza_de_Hogar/segundo_nombre'])) {
                    $nuevaEncuestaKobo->segundo_nombre = $encuestaKobo['Cabeza_de_Hogar/segundo_nombre'];
                }

                if (isset($encuestaKobo['Cabeza_de_Hogar/_C_mo_es_tu_primer_apellido'])) {
                    $nuevaEncuestaKobo->primer_apellido = $encuestaKobo['Cabeza_de_Hogar/_C_mo_es_tu_primer_apellido'];
                }
                if (isset($encuestaKobo['Cabeza_de_Hogar/primer_apellido'])) {
                    $nuevaEncuestaKobo->primer_apellido = $encuestaKobo['Cabeza_de_Hogar/primer_apellido'];
                }

                if (isset($encuestaKobo['Cabeza_de_Hogar/_C_mo_es_tu_segundo_apellido'])) {
                    $nuevaEncuestaKobo->segundo_apellido = $encuestaKobo['Cabeza_de_Hogar/_C_mo_es_tu_segundo_apellido'];
                }
                if (isset($encuestaKobo['Cabeza_de_Hogar/segundo_apellido'])) {
                    $nuevaEncuestaKobo->segundo_apellido = $encuestaKobo['Cabeza_de_Hogar/segundo_apellido'];
                }

                if (isset($encuestaKobo['Cabeza_de_Hogar/Cu_l_es_su_tipo_de_documento_d'])) {
                    $nuevaEncuestaKobo->tipo_documento = $encuestaKobo['Cabeza_de_Hogar/Cu_l_es_su_tipo_de_documento_d'];
                }
                if (isset($encuestaKobo['Cabeza_de_Hogar/tipo_documento'])) {
                    $nuevaEncuestaKobo->tipo_documento = $encuestaKobo['Cabeza_de_Hogar/tipo_documento'];
                }

                if (isset($encuestaKobo['Cabeza_de_Hogar/_Qu_otro_tipo_de_documento'])) {
                    $nuevaEncuestaKobo->cual_otro_tipo_documento = $encuestaKobo['Cabeza_de_Hogar/_Qu_otro_tipo_de_documento'];
                }

                if (isset($encuestaKobo['Cabeza_de_Hogar/_Cu_l_es_tu_n_mero_de_document'])) {
                    $nuevaEncuestaKobo->numero_documento = $encuestaKobo['Cabeza_de_Hogar/_Cu_l_es_tu_n_mero_de_document'];
                }
                if (isset($encuestaKobo['Cabeza_de_Hogar/numero_documento'])) {
                    $nuevaEncuestaKobo->numero_documento = $encuestaKobo['Cabeza_de_Hogar/numero_documento'];
                }

                if (isset($encuestaKobo['Caracterizacion_GF/Fecha_llegada'])) {
                    $nuevaEncuestaKobo->fecha_llegada_pais = date("Y-m-d", strtotime($encuestaKobo['Caracterizacion_GF/Fecha_llegada']));
                }
                if (isset($encuestaKobo['group_bc6si92/fecha_llegada'])) {
                    $nuevaEncuestaKobo->fecha_llegada_pais = date("Y-m-d", strtotime($encuestaKobo['group_bc6si92/fecha_llegada']));
                }

                if (isset($encuestaKobo['Caracterizacion_GF/_Cu_l_es_tu_destino_final_dent'])) {
                    $nuevaEncuestaKobo->nombre_municipio_destino_final = $encuestaKobo['Caracterizacion_GF/_Cu_l_es_tu_destino_final_dent'];
                }
                if (isset($encuestaKobo['group_bc6si92/destino_final'])) {
                    $nuevaEncuestaKobo->nombre_municipio_destino_final = $encuestaKobo['group_bc6si92/destino_final'];
                }

                if (isset($encuestaKobo['group_bc6si92/telefono'])) {
                    $nuevaEncuestaKobo->numero_contacto = $encuestaKobo['group_bc6si92/telefono'];
                }
                if (isset($encuestaKobo['group_bc6si92/N_mero_de_Tel_fono_1'])) {
                    $nuevaEncuestaKobo->numero_contacto = $encuestaKobo['group_bc6si92/N_mero_de_Tel_fono_1'];
                }

                //$nuevaEncuestaKobo->save();

                if (isset($encuestaKobo['group_bc6si92/entregado_venesperanza'])) {
                    $nuevaEncuestaKobo->numero_entregado_venesperanza = $encuestaKobo['group_bc6si92/entregado_venesperanza'];
                }
                if (isset($encuestaKobo['group_bc6si92/_Este_n_mero_te_fue_ado_por_Venesperanza'])) {

                    if ($encuestaKobo['group_bc6si92/_Este_n_mero_te_fue_ado_por_Venesperanza'] === 'no') {

                        $nuevaEncuestaKobo->numero_entregado_venesperanza = 0;

                    } else if ($encuestaKobo['group_bc6si92/_Este_n_mero_te_fue_ado_por_Venesperanza'] === 's') {
                        $nuevaEncuestaKobo->numero_entregado_venesperanza = 1;
                    }
                }

                if (isset($encuestaKobo['group_bc6si92/linea_propia'])) {
                    $nuevaEncuestaKobo->linea_contacto_propia = $encuestaKobo['group_bc6si92/linea_propia'];
                }

                if (isset($encuestaKobo['group_bc6si92/linea_asociada_whatsapp'])) {
                    $nuevaEncuestaKobo->linea_asociada_whatsapp = $encuestaKobo['group_bc6si92/linea_asociada_whatsapp'];
                }
                if (isset($encuestaKobo['group_bc6si92/_Esta_l_nea_est_asociada_a_Whatsapp'])) {

                    if ($encuestaKobo['group_bc6si92/_Esta_l_nea_est_asociada_a_Whatsapp'] === 'no') {

                        $nuevaEncuestaKobo->linea_asociada_whatsapp = 0;

                    } else if ($encuestaKobo['group_bc6si92/_Esta_l_nea_est_asociada_a_Whatsapp'] === 's') {
                        $nuevaEncuestaKobo->linea_asociada_whatsapp = 1;
                    }

                }

                if (isset($encuestaKobo['group_bc6si92/correo'])) {
                    $nuevaEncuestaKobo->correo_electronico = $encuestaKobo['group_bc6si92/correo'];
                }
                if (isset($encuestaKobo['group_bc6si92/_Tienes_un_correo_el_ico_para_contactarte'])) {
                    $nuevaEncuestaKobo->correo_electronico = $encuestaKobo['group_bc6si92/_Tienes_un_correo_el_ico_para_contactarte'];
                }

                //return $nuevaEncuestaKobo;

                if ($nuevaEncuestaKobo->primer_nombre || $nuevaEncuestaKobo->primer_apellido) {
                    $nuevaEncuestaKobo->save();
                    //Autorizacion
                    $autorizacion = new Autorizacion;

                    $autorizacion->id_encuesta = $nuevaEncuestaKobo->id;

                    if (isset($encuestaKobo['Consentimiento/Autorizo_el_tratamiento_de_mis'])) {

                        if ($encuestaKobo['Consentimiento/Autorizo_el_tratamiento_de_mis'] === 's') {

                            $autorizacion->tratamiento_datos = 1;

                        } else {
                            $autorizacion->tratamiento_datos = 0;
                        }
                        //return $kobo['Consentimiento/Autorizo_el_tratamiento_de_mis'];
                        $totalkobo += 1;
                    }

                    if (isset($encuestaKobo['Consentimiento/Entiendo_y_acepto_lo_cipar_en_la_encuesta'])) {

                        if ($encuestaKobo['Consentimiento/Entiendo_y_acepto_lo_cipar_en_la_encuesta'] === 's') {
                            $autorizacion->terminos_condiciones = 1;
                            $autorizacion->condiciones = 1;
                        } else {
                            $autorizacion->terminos_condiciones = 0;
                            $autorizacion->condiciones = 0;
                        }
                    }

                    $autorizacion->save();
                }

            }

            //un solo caso
            /*
        $encuesta_kobo_existe = Encuesta::where('fuente','=',3)->where('id_kobo','=',$formulariosKobo[1]['_id'])->first();

        if(!$encuesta_kobo_existe){
        array_push( $respuestasKobo, $formulariosKobo[1]['_id']);
        //cada kobo que no exista en la tabla encuesta de base datos, crea el registro nuevo con la info del kobo
        //cada campo del kobo convertirlo al campo de la base datos
        $nuevaEncuestaKobo = new Encuesta;

        $nuevaEncuestaKobo->id_kobo = $formulariosKobo[1]['_id'];
        $nuevaEncuestaKobo->fuente = 3;

        if(isset($formulariosKobo[1]['Cabeza_de_Hogar/C_mo_es_su_primer_nombre'])){
        $nuevaEncuestaKobo->primer_nombre = $formulariosKobo[1]['Cabeza_de_Hogar/C_mo_es_su_primer_nombre'];
        }
        if(isset($formulariosKobo[1]['Cabeza_de_Hogar/primer_nombre'])){
        $nuevaEncuestaKobo->primer_nombre = $formulariosKobo[1]['Cabeza_de_Hogar/primer_nombre'];
        }

        if(isset($formulariosKobo[1]['Cabeza_de_Hogar/C_mo_es_su_segundo_nombre'])){
        $nuevaEncuestaKobo->segundo_nombre = $formulariosKobo[1]['Cabeza_de_Hogar/C_mo_es_su_segundo_nombre'];
        }
        if(isset($formulariosKobo[1]['Cabeza_de_Hogar/segundo_nombre'])){
        $nuevaEncuestaKobo->segundo_nombre = $formulariosKobo[1]['Cabeza_de_Hogar/segundo_nombre'];
        }

        if(isset($formulariosKobo[1]['Cabeza_de_Hogar/_C_mo_es_tu_primer_apellido'])){
        $nuevaEncuestaKobo->primer_apellido = $formulariosKobo[1]['Cabeza_de_Hogar/_C_mo_es_tu_primer_apellido'];
        }

        if(isset($formulariosKobo[1]['Cabeza_de_Hogar/primer_apellido'])){
        $nuevaEncuestaKobo->primer_apellido = $formulariosKobo[1]['Cabeza_de_Hogar/primer_apellido'];
        }

        if(isset($formulariosKobo[1]['Cabeza_de_Hogar/_C_mo_es_tu_segundo_apellido'])){
        $nuevaEncuestaKobo->segundo_apellido = $formulariosKobo[1]['Cabeza_de_Hogar/_C_mo_es_tu_segundo_apellido'];
        }
        if(isset($formulariosKobo[1]['Cabeza_de_Hogar/segundo_apellido'])){
        $nuevaEncuestaKobo->segundo_apellido = $formulariosKobo[1]['Cabeza_de_Hogar/segundo_apellido'];
        }

        if(isset($formulariosKobo[1]['Cabeza_de_Hogar/Cu_l_es_su_tipo_de_documento_d'])){
        $nuevaEncuestaKobo->tipo_documento = $formulariosKobo[1]['Cabeza_de_Hogar/Cu_l_es_su_tipo_de_documento_d'];
        }
        if(isset($formulariosKobo[1]['Cabeza_de_Hogar/tipo_documento'])){
        $nuevaEncuestaKobo->cual_otro_tipo_documento = $formulariosKobo[1]['Cabeza_de_Hogar/tipo_documento'];
        }

        if(isset($formulariosKobo[1]['Cabeza_de_Hogar/_Qu_otro_tipo_de_documento'])){
        $nuevaEncuestaKobo->cual_otro_tipo_documento = $formulariosKobo[1]['Cabeza_de_Hogar/_Qu_otro_tipo_de_documento'];
        }

        if(isset($formulariosKobo[1]['Cabeza_de_Hogar/_Cu_l_es_tu_n_mero_de_document'])){
        $nuevaEncuestaKobo->numero_documento = $formulariosKobo[1]['Cabeza_de_Hogar/_Cu_l_es_tu_n_mero_de_document'];
        }

        if(isset($formulariosKobo[1]['Cabeza_de_Hogar/numero_documento'])){
        $nuevaEncuestaKobo->numero_documento = $formulariosKobo[1]['Cabeza_de_Hogar/numero_documento'];
        }

        if(isset($formulariosKobo[1]['Caracterizacion_GF/Fecha_llegada'])){
        $nuevaEncuestaKobo->fecha_llegada_pais = date("Y-m-d", strtotime($formulariosKobo[1]['Caracterizacion_GF/Fecha_llegada']));

        }

        if(isset($formulariosKobo[1]['group_bc6si92/fecha_llegada'])){
        $nuevaEncuestaKobo->fecha_llegada_pais = date("Y-m-d", strtotime($formulariosKobo[1]['group_bc6si92/fecha_llegada']));
        }

        if(isset($formulariosKobo[1]['Caracterizacion_GF/_Cu_l_es_tu_destino_final_dent'])){
        $nuevaEncuestaKobo->nombre_municipio_destino_final = $formulariosKobo[1]['Caracterizacion_GF/_Cu_l_es_tu_destino_final_dent'];
        }

        if(isset($formulariosKobo[1]['group_bc6si92/destino_final'])){
        $nuevaEncuestaKobo->nombre_municipio_destino_final = $formulariosKobo[1]['group_bc6si92/destino_final'];
        }

        if(isset($formulariosKobo[1]['group_bc6si92/telefono'])){
        $nuevaEncuestaKobo->numero_contacto = $formulariosKobo[1]['group_bc6si92/telefono'];
        }

        if(isset($formulariosKobo[1]['group_bc6si92/N_mero_de_Tel_fono_1'])){
        $nuevaEncuestaKobo->numero_contacto = $formulariosKobo[1]['group_bc6si92/N_mero_de_Tel_fono_1'];
        }

        if(isset($formulariosKobo[1]['group_bc6si92/entregado_venesperanza'])){
        $nuevaEncuestaKobo->numero_entregado_venesperanza = $formulariosKobo[1]['group_bc6si92/entregado_venesperanza'];
        }

        if(isset($formulariosKobo[1]['group_bc6si92/_Este_n_mero_te_fue_ado_por_Venesperanza'])){
        if($formulariosKobo[1]['group_bc6si92/_Este_n_mero_te_fue_ado_por_Venesperanza'] === 'no'){

        $nuevaEncuestaKobo->numero_entregado_venesperanza = 0;

        }else if($formulariosKobo[1]['group_bc6si92/_Este_n_mero_te_fue_ado_por_Venesperanza'] === 's'){
        $nuevaEncuestaKobo->numero_entregado_venesperanza = 1;
        }
        }

        if(isset($formulariosKobo[1]['group_bc6si92/linea_propia'])){
        $nuevaEncuestaKobo->linea_contacto_propia = $formulariosKobo[0]['group_bc6si92/linea_propia'];
        }

        if(isset($formulariosKobo[1]['group_bc6si92/linea_asociada_whatsapp'])){
        $nuevaEncuestaKobo->linea_asociada_whatsapp = $formulariosKobo[0]['group_bc6si92/linea_asociada_whatsapp'];
        }

        if(isset($formulariosKobo[1]['group_bc6si92/_Esta_l_nea_est_asociada_a_Whatsapp'])){

        if($formulariosKobo[1]['group_bc6si92/_Esta_l_nea_est_asociada_a_Whatsapp'] === 'no'){

        $nuevaEncuestaKobo->linea_asociada_whatsapp = 0;

        }else if($formulariosKobo[1]['group_bc6si92/_Esta_l_nea_est_asociada_a_Whatsapp'] === 's'){
        $nuevaEncuestaKobo->linea_asociada_whatsapp = 1;
        }
        }

        if(isset($formulariosKobo[1]['group_bc6si92/correo'])){
        $nuevaEncuestaKobo->correo_electronico = $formulariosKobo[1]['group_bc6si92/correo'];
        }

        if(isset($formulariosKobo[1]['group_bc6si92/_Tienes_un_correo_el_ico_para_contactarte'])){
        $nuevaEncuestaKobo->correo_electronico = $formulariosKobo[1]['group_bc6si92/_Tienes_un_correo_el_ico_para_contactarte'];
        }

        $nuevaEncuestaKobo->save();

        //Autorizacion
        $autorizacion = new Autorizacion;

        $autorizacion->id_encuesta = $nuevaEncuestaKobo->id;

        if(isset($formulariosKobo[1]['Consentimiento/Autorizo_el_tratamiento_de_mis'])){

        if($formulariosKobo[1]['Consentimiento/Autorizo_el_tratamiento_de_mis'] === 's'){

        $autorizacion->tratamiento_datos = 1;

        }else{
        $autorizacion->tratamiento_datos = 0;
        }
        //return $kobo['Consentimiento/Autorizo_el_tratamiento_de_mis'];
        $totalkobo += 1;
        }

        if(isset($formulariosKobo[1]['Consentimiento/Entiendo_y_acepto_lo_cipar_en_la_encuesta'])){

        if($formulariosKobo[1]['Consentimiento/Entiendo_y_acepto_lo_cipar_en_la_encuesta'] === 's'){
        $autorizacion->terminos_condiciones = 1;
        $autorizacion->condiciones = 1;
        }else{
        $autorizacion->terminos_condiciones = 0;
        $autorizacion->condiciones = 0;
        }
        }

        $autorizacion->save();

        }
        //un solo caso
         */

        }

        //return $totalkobo;
        //return $formulariosKobo[1];
    }
}
