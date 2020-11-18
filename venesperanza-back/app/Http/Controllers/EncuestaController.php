<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Encuesta;
use App\Models\MiembrosHogar;
use App\Models\Autorizacion;
use App\Models\Webhook;

use DateTime;


class EncuestaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $ben = new Encuesta;
            $ben->paso = $request['paso'];
            $ben->primer_nombre = $request['infoencuesta']['firstNameCtrl'];
            $ben->segundo_nombre = $request['infoencuesta']['secondNameCtrl'];
            $ben->primer_apellido = $request['infoencuesta']['lastNameCtrl'];
            $ben->segundo_apellido = $request['infoencuesta']['secondLastNameCtrl'];
            $ben->sexo = $request['infoencuesta']['sexoCtrl'];
            if ($ben->sexo === "otro") {
                $ben->otrosexo = $request['infoencuesta']['otroSexoCtrl'];
            }
            //$ben->fecha_nacimiento = date_format(strtotime($request['infoencuesta']['fechaNacimientoCtrl']),"y-m-d");
            $ben->fecha_nacimiento = date("Y-m-d", strtotime($request['infoencuesta']['fechaNacimientoCtrl']));
            $ben->nacionalidad = $request['infoencuesta']['nacionalidadCtrl'];
            $ben->tipo_documento = $request['infoencuesta']['tipoDocumentoCtrl'];
            if ($ben->tipo_documento == "Otro") {
                $ben->cual_otro_tipo_documento = $request['infoencuesta']['otroTipoDocumentoCtrl'];
            }
            if ($ben->tipo_documento != "Indocumentado") {
                $ben->numero_documento = $request['infoencuesta']['numeroDocumentoCtrl'];
            }

            //codigo_encuesta
            $nombreiniciales = mb_strtoupper(mb_substr ( $ben->primer_nombre, 0,2 ));
            //$nombreiniciales =  str_replace( strtoupper(substr ( $ben->primer_nombre, 0,2 )), ['Á','É','Í','Ó','Ú','Ñ','Ü'],['A','E','I','O','U','Ñ','U']);
            //$nombreiniciales = str_replace($nombreiniciales, ['Á','É','Í','Ó','Ú','Ñ','Ü'],['A','E','I','O','U','Ñ','U']);
            $nombreiniciales2 = strtr($nombreiniciales,array('Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ñ'=>'N','Ü','U'));
            $nombreiniciales3 = str_replace('Ü','U',$nombreiniciales2);


            $apellidoiniciales = mb_strtoupper(mb_substr( $ben->primer_apellido,0,2 ));
            //$apellidoiniciales = str_replace( strtoupper( substr ($ben->primer_apellido,0,2 )), ['Á','É','Í','Ó','Ú','Ñ','Ü'],['A','E','I','O','U','Ñ','U']);
            //$apellidoiniciales = str_replace($apellidoiniciales, ['Á','É','Í','Ó','Ú','Ñ','Ü'],['A','E','I','O','U','Ñ','U']);
            $apellidoiniciales2 = strtr($apellidoiniciales, array('Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ñ'=>'N','Ü','U'));
            $apellidoiniciales3 = str_replace('Ü','U',$apellidoiniciales2);
            //return ['nombreeditado'=> $nombreiniciales3, 'apellidoeditado'=>$apellidoiniciales3];
            
            //$fecha1= mktime(0,0,0,01,01,1970);

            $fecha1= new DateTime("1900-01-01");
            //$fecha2= new DateTime("2017-08-04");
            $fecha2= new DateTime($ben->fecha_nacimiento);
            $diff = $fecha1->diff($fecha2);

            //return ['diff'=>$diff];
            $diferenciaDias = $diff->days;

            $sexoinicial = strtoupper(substr( $ben->sexo,0,1));
           
            
            
            $ben->codigo_encuesta = $nombreiniciales3.$apellidoiniciales3.$diferenciaDias.$sexoinicial;
            //return $ben->codigo_encuesta;
            $ben->save();
            $autorizacion = Autorizacion::find($request['autorizacion_id']);
            $autorizacion->id_encuesta = $ben->id;
            $autorizacion->save();
            return $ben->id;

        } catch (Exception $e) {
            return $e;
            //throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        try {
            $encuesta = Encuesta::find($id);
            if ($encuesta) {
                switch ($request['paso']) {
                    case "paso1":
                        $encuesta->primer_nombre = $request['infoencuesta']['firstNameCtrl'];
                        $encuesta->segundo_nombre = $request['infoencuesta']['secondNameCtrl'];
                        $encuesta->primer_apellido = $request['infoencuesta']['lastNameCtrl'];
                        $encuesta->segundo_apellido = $request['infoencuesta']['secondLastNameCtrl'];
                        $encuesta->sexo = $request['infoencuesta']['sexoCtrl'];
                        /*
                        if ($encuesta->sexo === "otro") {
                            $encuesta->otrosexo = $request['infoencuesta']['otroSexoCtrl'];
                        } else {
                            $encuesta->otrosexo = null;
                        }
                        */
                        //$encuesta->fecha_nacimiento = date("d/m/Y", strtotime($request['infoencuesta']['fechaNacimientoCtrl']));
                        $encuesta->fecha_nacimiento = date("Y-m-d", strtotime($request['infoencuesta']['fechaNacimientoCtrl']));
                        $encuesta->nacionalidad = $request['infoencuesta']['nacionalidadCtrl'];
                        $encuesta->tipo_documento = $request['infoencuesta']['tipoDocumentoCtrl'];
                        if ($encuesta->tipo_documento == "Otro") {
                            $encuesta->cual_otro_tipo_documento = $request['infoencuesta']['otroTipoDocumentoCtrl'];
                        }
                        if ($encuesta->tipo_documento != "Indocumentado") {
                            $encuesta->numero_documento = $request['infoencuesta']['numeroDocumentoCtrl'];
                        }

                        //codigo_encuesta
                        $nombreiniciales = mb_strtoupper(mb_substr ( $encuesta->primer_nombre, 0,2 ));
                        $nombreiniciales2 = strtr($nombreiniciales,array('Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ñ'=>'N','Ü','U'));
                        $nombreiniciales3 = str_replace('Ü','U',$nombreiniciales2);


                        $apellidoiniciales = mb_strtoupper(mb_substr( $encuesta->primer_apellido,0,2 ));
                        $apellidoiniciales2 = strtr($apellidoiniciales, array('Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ñ'=>'N','Ü','U'));
                        $apellidoiniciales3 = str_replace('Ü','U',$apellidoiniciales2);
                        

                        $fecha1= new DateTime("1900-01-01");
                        $fecha2= new DateTime($encuesta->fecha_nacimiento);
                        $diff = $fecha1->diff($fecha2);

                        $diferenciaDias = $diff->days;

                        $sexoinicial = strtoupper(substr( $encuesta->sexo,0,1));
                    
                        
                        
                        $encuesta->codigo_encuesta = $nombreiniciales3.$apellidoiniciales3.$diferenciaDias.$sexoinicial;
                        

                        if ($encuesta->save()) {
                            return $encuesta->id;
                        } else {
                            return "error";
                        }
                        break;
                    case "paso2":
                        if ($request['infoencuesta']['departamentoCtrl'] != "" && $request['infoencuesta']['municipioCtrl'] != ""
                            && $request['infoencuesta']['barrioCtrl'] != "" && $request['infoencuesta']['numeroContactoCtrl'] != ""
                            && $request['infoencuesta']['lineaContactoPropiaCtrl'] != "") {
                            $encuesta->paso = $request['paso'];
                            $encuesta->id_departamento = $request['infoencuesta']['departamentoCtrl'];
                            $encuesta->id_municipio = $request['infoencuesta']['municipioCtrl'];
                            $encuesta->barrio = $request['infoencuesta']['barrioCtrl'];
                            $encuesta->direccion = $request['infoencuesta']['direccionCtrl'];
                            $encuesta->numero_contacto = $request['infoencuesta']['numeroContactoCtrl'];

                            if ($request['infoencuesta']['lineaContactoPropiaCtrl'] === 'si') {
                                $encuesta->linea_contacto_propia = 1;

                                if ($request['infoencuesta']['lineaContactoAsociadaAWhatsappCtrl'] == 'si') {
                                    $encuesta->linea_asociada_whatsapp = 1;

                                } else if ($request['infoencuesta']['lineaContactoAsociadaAWhatsappCtrl'] == 'no') {
                                    $encuesta->linea_asociada_whatsapp = 0;
                                }

                            } else if ($request['infoencuesta']['lineaContactoPropiaCtrl'] === "no") {
                                $encuesta->linea_contacto_propia = 0;
                                $encuesta->linea_asociada_whatsapp = 0;

                                $encuesta->preguntar_en_caso_de_llamar = $request['infoencuesta']['contactoAlternativoCtrl'];

                            }
                            $encuesta->correo_electronico = $request['infoencuesta']['correoCtrl'];
                            $encuesta->comentario = $request['infoencuesta']['comentarioAdicionalCtrl'];
                            $encuesta->save();
                            if ($encuesta) {
                                return $encuesta->id;
                            } else {
                                return "error";
                            };
                        } else {
                            return "error";
                        }
                        break;
                    case "paso3":

                        //calcular Ratio:
                        $miembros_0_17_años = 0;
                        $miembros_18_59_años = 0;
                        $miembros_mas_60_años = 0;

                        //Calculo edad del miembro principal
                        $fecha1= new DateTime();
                        
                        $fecha2= new DateTime($encuesta->fecha_nacimiento);
                        $diff = $fecha1->diff($fecha2);

                        $diferenciaAñosMiembroPrincipal = $diff->y;

                        

                        if($diferenciaAñosMiembroPrincipal>=0 && $diferenciaAñosMiembroPrincipal<=17){
                            $miembros_0_17_años += 1;
                        }elseif($diferenciaAñosMiembroPrincipal >= 18 && $diferenciaAñosMiembroPrincipal<= 59){
                            $miembros_18_59_años += 1;
                        }elseif($diferenciaAñosMiembroPrincipal >= 60){
                            $miembros_mas_60_años += 1;
                        }

                        


                        $encuesta->paso = $request['paso'];
                        $miembrosexistentes = MiembrosHogar::where('id_encuesta', $encuesta->id)->delete();
                        if (count($request['infoencuesta']['miembrosFamilia']) > 0) {
                            $guardado = false;
                            foreach ($request['infoencuesta']['miembrosFamilia'] as $miembro) {
                                $addMiembro = new MiembrosHogar;
                                if ($addMiembro) {
                                    $addMiembro->id_encuesta = $encuesta->id;
                                    $addMiembro->primer_nombre_miembro = $miembro['primernombreCtrl'];
                                    $addMiembro->segundo_nombre_miembro = $miembro['segundonombreCtrl'];
                                    $addMiembro->primer_apellido_miembro = $miembro['primerapellidoCtrl'];
                                    $addMiembro->segundo_apellido_miembro = $miembro['segundoapellidoCtrl'];
                                    $addMiembro->sexo_miembro = $miembro['sexoCtrl'];
                                    /*
                                    if ($addMiembro->sexo_miembro === "otro") {
                                        $addMiembro->otrosexo_miembro = $miembro['otroSexoCtrl'];
                                    }
                                    */
                                    //$addMiembro->fecha_nacimiento = date("d/m/Y", strtotime($miembro['fechaCtrl']));
                                    $addMiembro->fecha_nacimiento = date("Y-m-d", strtotime($miembro['fechaCtrl']));

                                    //calculo edad de cada miembro de familia
                                    $fecha3= new DateTime($addMiembro->fecha_nacimiento);
                                    $diff = $fecha1->diff($fecha3);

                                    $diferenciaAñosMiembroSegundario = $diff->y;

                                    if($diferenciaAñosMiembroSegundario>=0 && $diferenciaAñosMiembroSegundario<=17){
                                        $miembros_0_17_años += 1;
                                    }elseif($diferenciaAñosMiembroSegundario >= 18 && $diferenciaAñosMiembroSegundario<= 59){
                                        $miembros_18_59_años += 1;
                                    }elseif($diferenciaAñosMiembroSegundario >= 60){
                                        $miembros_mas_60_años += 1;
                                    }
                                    


                                    if ($addMiembro->save()) {
                                        $guardado = true;
                                    }
                                }
                            }

                            $encuesta->unico_miembro_hogar = false;

                            //Calculo de Puntaje

                            if($miembros_18_59_años >= 1){

                                $division1 = ($miembros_0_17_años + $miembros_mas_60_años) / ($miembros_18_59_años );

                                $division = round($division1,1);

                            }else{
                                $division = 0.0;
                            }
                            
                            //return $division;
                            $puntaje = 0.0;

                            if($division<=0.6){

                                $puntaje = 0.0;
                                
                            }elseif ($division >= 0.7 && $division <= 1.2) {
                                $puntaje = 1.0;
                            }elseif ($division >= 1.3 && $division <= 1.8) {
                                $puntaje = 2.0;
                            }elseif ($division >= 1.9) {
                                $puntaje = 3.0;
                            }else{
                                $puntaje = 0.0;
                            }

                            //return $puntaje;

                            $encuesta->puntaje_paso_tres = $puntaje;


                            if($encuesta->gasto_hogar == 0){
                                //Cuando SI hay gasto de hogar calcula nuevamente el gasto percapita
                                
                                $gastos_percapita = $encuesta->total_gastos / ($miembros_0_17_años + $miembros_mas_60_años + $miembros_18_59_años);
                                
                                $encuesta->gastos_percapita1 = $gastos_percapita;
                                //return $encuesta;
                            }

                            

                            if ($encuesta->save() && $guardado == true) {
                                return ['encuesta' => $encuesta->id, 'Estado:' => 'Info Guardada'];
                            } else {
                                return ['encuesta' => $encuesta->id, 'Estado:' => 'Info NO Guardada'];
                            }
                        } else {

                            if($encuesta->gasto_hogar == 0){
                                
                                $gastos_percapita = $encuesta->total_gastos / ($miembros_0_17_años + $miembros_mas_60_años + $miembros_18_59_años);
                                $encuesta->gastos_percapita1 = $gastos_percapita;
                                //return $encuesta;
                            }

                            $encuesta->unico_miembro_hogar = true;

                            //Calculo de puntaje

                        
                            $puntaje = 0.0;

                            $encuesta->puntaje_paso_tres = $puntaje;


                            $encuesta->save();
                            return ['encuesta' => $encuesta->id, 'Estado:' => 'Sin otros miembros de Hogar'];
                        }
                        break;
                    case "paso4":

                        $puntaje_paso_cuatro = 0;
                        
                        $encuesta->paso = $request['paso'];
                        $encuesta->mujeres_embarazadas = $request['infoencuesta']['mujeresEmbarazadasCtrl'];
                        
                        //calculo puntaje
                        if($encuesta->mujeres_embarazadas && $encuesta->mujeres_embarazadas == 1){
                            $puntaje_paso_cuatro  += 2;
                        }

                        $encuesta->mujeres_lactantes = $request['infoencuesta']['mujeresLactantesCtrl'];

                        if($encuesta->mujeres_lactantes && $encuesta->mujeres_lactantes == 1){
                            $puntaje_paso_cuatro  += 2;
                        }

                        $encuesta->situacion_discapacidad = $request['infoencuesta']['personasDiscapacidadCtrl'];

                        if($encuesta->situacion_discapacidad && $encuesta->situacion_discapacidad == 1){
                            $puntaje_paso_cuatro  += 2;
                        }

                        $encuesta->enfermedades_cronicas = $request['infoencuesta']['personasEnfermedadesCronicasCtrl'];
                        
                        if($encuesta->enfermedades_cronicas && $encuesta->enfermedades_cronicas == 1){
                            $puntaje_paso_cuatro  += 2;
                        }

                        //return $puntaje_paso_cinco;
                        $encuesta->puntaje_paso_cuatro = $puntaje_paso_cuatro;

                        $encuesta->save();
                        if ($encuesta) {
                            return $encuesta->id;
                        } else {
                            return "error";
                        }
                        break;
                    case "paso5":

                        $puntaje_paso_cinco = 0;

                        $encuesta->paso = $request['paso'];

                        $encuesta->falta_comida = $request['infoencuesta']['alimentos11Ctrl'];

                        //calculo puntaje

                        //para falta de comida
                        if($encuesta->falta_comida == 1){
                            $encuesta->cuantas_veces_falta_comida = $request['infoencuesta']['alimentos12Ctrl'];

                            if($encuesta->cuantas_veces_falta_comida == 'pocas_veces_1-2_veces'){
                                $puntaje_paso_cinco += 1;
                            }elseif($encuesta->cuantas_veces_falta_comida == 'algunas_veces_3-10_veces'){
                                $puntaje_paso_cinco += 2;
                            }elseif($encuesta->cuantas_veces_falta_comida == 'muchas_veces_mas_de_10_veces'){
                                $puntaje_paso_cinco += 3;
                            }
                        }else{
                            $encuesta->cuantas_veces_falta_comida = null;
                        }
                        
                        //para dormir sin comer

                        $encuesta->dormir_sin_comer = $request['infoencuesta']['alimentos13Ctrl'];

                        if($encuesta->dormir_sin_comer == 1){

                            $encuesta->cuantas_veces_dormir_sin_comer = $request['infoencuesta']['alimentos14Ctrl'];

                            if($encuesta->cuantas_veces_dormir_sin_comer == 'pocas_veces_1-2_veces'){
                                $puntaje_paso_cinco += 1;
                            }elseif($encuesta->cuantas_veces_dormir_sin_comer == 'algunas_veces_3-10_veces'){
                                $puntaje_paso_cinco += 2;
                            }elseif($encuesta->cuantas_veces_dormir_sin_comer == 'muchas_veces_mas_de_10_veces'){
                                $puntaje_paso_cinco += 3;
                            }

                        }else{
                            $encuesta->cuantas_veces_dormir_sin_comer = null;
                        }


                        //para todo dia sin comer
                        $encuesta->todo_dia_sin_comer = $request['infoencuesta']['alimentos15Ctrl'];

                        if($encuesta->todo_dia_sin_comer == 1){

                            $encuesta->cuantas_veces_todo_dia_sin_comer = $request['infoencuesta']['alimentos16Ctrl'];

                            if($encuesta->cuantas_veces_todo_dia_sin_comer == 'pocas_veces_1-2_veces'){
                                $puntaje_paso_cinco += 1;
                            }elseif($encuesta->cuantas_veces_todo_dia_sin_comer == 'algunas_veces_3-10_veces'){
                                $puntaje_paso_cinco += 2;
                            }elseif($encuesta->cuantas_veces_todo_dia_sin_comer == 'muchas_veces_mas_de_10_veces'){
                                $puntaje_paso_cinco += 3;
                            }

                        }else{
                            $encuesta->cuantas_veces_todo_dia_sin_comer = null;
                        }

                        $encuesta->puntaje_paso_cinco = $puntaje_paso_cinco;

                        $encuesta->save();
                        if ($encuesta) {
                            return $encuesta->id;
                        } else {
                            return "error";
                        }
                        break;
                    case "paso6":
                        if ($request['infoencuesta']['necesidades17Ctrl'] != "") {

                            $puntaje_paso_seis = 0;

                            $encuesta->paso = $request['paso'];
                            $encuesta->satisfaccion_necesidades_basicas = $request['infoencuesta']['necesidades17Ctrl'];

                            //calculo puntaje
                            if($encuesta->satisfaccion_necesidades_basicas == 'todas'){
                                $puntaje_paso_seis += 0;
                            }elseif($encuesta->satisfaccion_necesidades_basicas == 'lamayoria'){
                                $puntaje_paso_seis += 1;
                            }elseif($encuesta->satisfaccion_necesidades_basicas == 'algunas'){
                                $puntaje_paso_seis += 2;
                            }elseif($encuesta->satisfaccion_necesidades_basicas == 'ninguna'){
                                $puntaje_paso_seis += 3;
                            }
                            $encuesta->necesidadesbasicas()->detach();

                        
                            if($encuesta->satisfaccion_necesidades_basicas == 'algunas' || $encuesta->satisfaccion_necesidades_basicas == 'ninguna' ){

                                if (sizeOf($request['infoencuesta']['necesidades22Ctrl']) > 0) {
                                    
                                    foreach ($request['infoencuesta']['necesidades22Ctrl'] as $necesidad) {
                                        $encuesta->necesidadesbasicas()->attach(intval($necesidad));

                                        if(intval($necesidad) == 1 || intval($necesidad) == 2 || intval($necesidad) == 8){
                                            $puntaje_paso_seis += 1;
                                        }

                                    }
                                }
                            }
                            

                            $encuesta->puntaje_paso_seis = $puntaje_paso_seis;

                            $encuesta->save();
                            if ($encuesta) {
                                return $encuesta->id;
                            } else {
                                return "error";
                            }
                        } else {
                            return "error";
                        }
                        break;
                    case "paso7":
                        $encuesta->paso = $request['paso'];

                        $puntaje_paso_siete = 0;

                        $encuesta->tipo_vivienda_alojamiento_15_dias = $request['infoencuesta']['alojamientoViviendaCtrl'];
                        
                        //calculo puntaje
                        if($encuesta->tipo_vivienda_alojamiento_15_dias == 'inquilinato' || $encuesta->tipo_vivienda_alojamiento_15_dias == 'techo_improvisado'){
                            $puntaje_paso_siete += 1;
                        }elseif($encuesta->tipo_vivienda_alojamiento_15_dias == 'albergue_mas5dias'){
                            $puntaje_paso_siete += 2;
                        }elseif($encuesta->tipo_vivienda_alojamiento_15_dias == 'situacion_calle'){
                            $puntaje_paso_siete += 3;
                        }

                        $encuesta->puntaje_paso_siete = $puntaje_paso_siete;
                        //return $encuesta->puntaje_paso_ocho;
                        
                        $encuesta->save();
                        if ($encuesta) {
                            return $encuesta->id;
                        } else {
                            return "error";
                        }
                    case "paso8":
                        $encuesta->paso = $request['paso'];
                        $encuesta->ingresos_c = $request['infoencuesta']['economicoCtrl'];

                        $puntaje_paso_ocho = 0;

                        //calculo puntaje
                        
                        if ($request['infoencuesta']['gastoHogarCtrl']) {
                            //selecciona que NO hay gasto de hogar
                            $encuesta->total_gastos = null;
                            $encuesta->gastos_percapita1 = null;
                            $encuesta->gasto_hogar = $request['infoencuesta']['gastoHogarCtrl'];
                        } else {
                            //selecciona que SI hay gasto de hogar e ingresa un valor
                            $encuesta->total_gastos = $request['infoencuesta']['gastoHogar7diasCtrl'];
                            $encuesta->gastos_percapita1 = $encuesta->total_gastos / $request['infoencuesta']['cantidad_miembros'];
                            

                            if($encuesta->gastos_percapita1 <= 29400){
                                $puntaje_paso_ocho = 3;
                            }elseif($encuesta->gastos_percapita1 >= 29401 && $encuesta->gastos_percapita1 <= 46900){

                                $puntaje_paso_ocho = 2;

                            }elseif($encuesta->gastos_percapita1 >= 46901 && $encuesta->gastos_percapita1 <= 64400){

                                $puntaje_paso_ocho = 1;

                            }elseif($encuesta->gastos_percapita1 >= 64401){

                                $puntaje_paso_ocho = 0;

                            }

                            $encuesta->gasto_hogar = false;
                        }

                        $encuesta->puntaje_paso_ocho = $puntaje_paso_ocho;

                        $encuesta->save();

                        return $encuesta->puntaje_paso_ocho;
                        
                        if ($encuesta) {
                            return $encuesta->id;
                        } else {
                            return "error";
                        }
                }
            } else {
                return "error";
            }
        } catch (Exception $e) {
            return "error";
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
