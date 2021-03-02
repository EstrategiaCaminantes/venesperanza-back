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



class EncuestaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    public function getEncuestas()
    {
        return Encuesta::with(['miembroshogar', 'necesidadesbasicas', 'departamento', 'municipio'])->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    public function dashboard()
    {
        $f = ['id', 'created_at', 'puntaje_paso_tres', 'puntaje_paso_cuatro', 'puntaje_paso_cinco',
            'puntaje_paso_seis', 'puntaje_paso_siete', 'puntaje_paso_ocho', 'paso'];
        $week = Encuesta::select($f)
            ->get()
            ->groupBy(function ($date) {
                $week = Carbon::parse($date->created_at);
                return $week->startOfWeek()->toDateString() . ' - ' . $week->endOfWeek()->toDateString();
            });
        $day = Encuesta::select($f)
            ->get()
            ->groupBy(function ($date) {
                $week = Carbon::parse($date->created_at);
                return $week->format('Y-m-d');
            });
        return array('day' => $day, 'week' => $week);
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
          
                //Nuevo PASO 1 que por ahora es PASO 0, crea la encuesta en bd
    
                $nuevaEncuesta = new Encuesta;
    
                $nuevaEncuesta->paso = $request['paso'];
    
                $nuevaEncuesta->como_llego_al_formulario = $request['infoencuesta']['comoLlegoAlFormularioCtrl'];

                if($nuevaEncuesta->como_llego_al_formulario == "Otro"){
                    $nuevaEncuesta->donde_encontro_formulario = $request['infoencuesta']['dondeEncontroFormularioCtrl'];
                }else{
                    $nuevaEncuesta->donde_encontro_formulario = null;
                }

                $nuevaEncuesta->fecha_llegada_pais = $request['infoencuesta']['llegadaDestinofechaLlegadaCtrl'];
                $nuevaEncuesta->estar_dentro_colombia = $request['infoencuesta']['llegadaDestinoPlaneaEstarEnColombiaCtrl']; //reciba 0-No, 1-Si, 2-NoEstoySeguro
    
                if($nuevaEncuesta->estar_dentro_colombia == 1 || $nuevaEncuesta->estar_dentro_colombia == 2){
    
                    if($request['infoencuesta']['llegadaDestinoDepartamentoCtrl'] != 'nodefinido'){
                        
                        $nuevaEncuesta->id_departamento_destino_final = $request['infoencuesta']['llegadaDestinoDepartamentoCtrl'];

                        if($request['infoencuesta']['llegadaDestinoCiudadCtrl'] != 'nodefinido'){
                            $nuevaEncuesta->id_municipio_destino_final = $request['infoencuesta']['llegadaDestinoCiudadCtrl'];

                        }
                    }
                   
                }else{
                    $nuevaEncuesta->id_departamento_destino_final = null;
                    $nuevaEncuesta->id_municipio_destino_final = null;

                    $nuevaEncuesta->pais_destino_final = $request['infoencuesta']['llegadaDestinoDestinoFinalFueraColombiaCtrl'];
                }
    
                $nuevaEncuesta->save();
                /*
                //Anterior PASO 1 primeros datos para crear encuesta
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
                $nombreiniciales = mb_strtoupper(mb_substr($ben->primer_nombre, 0, 2));
                $nombreiniciales = strtr($nombreiniciales, array('Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ñ' => 'N', 'Ü', 'U'));
                $nombreiniciales = str_replace('Ü', 'U', $nombreiniciales);
    
    
                $apellidoiniciales = mb_strtoupper(mb_substr($ben->primer_apellido, 0, 2));
                $apellidoiniciales = strtr($apellidoiniciales, array('Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ñ' => 'N', 'Ü', 'U'));
                $apellidoiniciales = str_replace('Ü', 'U', $apellidoiniciales);
    
                $fecha1900 = new DateTime("1900-01-01");
                $fecha2 = new DateTime($ben->fecha_nacimiento);
                $diff = $fecha1900->diff($fecha2);
                $diferenciaDias = $diff->days;
                $sexoinicial = strtoupper(substr($ben->sexo, 0, 1));
    
                $ben->codigo_encuesta = $nombreiniciales . $apellidoiniciales . $diferenciaDias . $sexoinicial;
                $ben->save();
                */
    
    
                $autorizacion = Autorizacion::find($request['autorizacion_id']);
                //$autorizacion->id_encuesta = $ben->id;
                $autorizacion->id_encuesta = $nuevaEncuesta->id;
                $autorizacion->save();
                //return $ben->id;
                return $nuevaEncuesta->id;
    
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


    public function asignarcodigospuntajes()
    {
        try {
            $encuestas = Encuesta::whereNotNull('codigo_encuesta')->with('necesidadesbasicas')->get();
            $nuevosDATOS = [];
            $fechaactual = new DateTime(); //fecha actual
            foreach ($encuestas as $encuesta) {
                $fecha2 = new DateTime($encuesta->fecha_nacimiento);
                //PUNTAJES PARA CADA PASO:
                //puntaje paso 3:
                //calcular Ratio:
                $miembros_0_17_anios = 0;
                $miembros_18_59_anios = 0;
                $miembros_mas_60_anios = 0;
                //Calculo edad del miembro principal
                $diff = $fechaactual->diff($fecha2);
                $diferenciaaniosMiembroPrincipal = $diff->y; //edad del miembro principal
                if ($diferenciaaniosMiembroPrincipal >= 0 && $diferenciaaniosMiembroPrincipal <= 17) {
                    $miembros_0_17_anios += 1;
                } elseif ($diferenciaaniosMiembroPrincipal >= 18 && $diferenciaaniosMiembroPrincipal <= 59) {
                    $miembros_18_59_anios += 1;
                } elseif ($diferenciaaniosMiembroPrincipal >= 60) {
                    $miembros_mas_60_anios += 1;
                }
                //Total Miembros familia para hacer calculos:
                $miembrosFamiliaTamanio = 1; //el 1 es el miembro principal de la encuesta
                //miembros segundarios de la encuesta:
                $miembros = MiembrosHogar::where('id_encuesta', $encuesta['id'])->get();
                $miembrosFamiliaTamanio += sizeof($miembros); //sumo a miembros de familia los miembros segundarios
                $puntaje3 = 0;
                if ($miembrosFamiliaTamanio > 0) {
                    //recorro los miembros:
                    $fecha1900 = new DateTime("1900-01-01");
                    foreach ($miembros as $miembro) {
                        //Asignar el codigo_encuesta:
                        $nombreinicialesM = mb_strtoupper(mb_substr($miembro->primer_nombre_miembro, 0, 2));
                        $nombreinicialesM = strtr($nombreinicialesM, array('Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ñ' => 'N', 'Ü', 'U'));
                        $nombreinicialesM = str_replace('Ü', 'U', $nombreinicialesM);
                        $apellidoinicialesM = mb_strtoupper(mb_substr($miembro->primer_apellido_miembro, 0, 2));
                        $apellidoinicialesM = strtr($apellidoinicialesM, array('Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ñ' => 'N', 'Ü', 'U'));
                        $apellidoinicialesM = str_replace('Ü', 'U', $apellidoinicialesM);
                        $fecha2 = new DateTime($miembro->fecha_nacimiento);
                        $diffM = $fecha1900->diff($fecha2);
                        $diferenciaDias = $diffM->days;
                        $sexoinicial = strtoupper(substr($miembro->sexo_miembro, 0, 1));
                        $miembro->codigo_encuesta = $nombreinicialesM . $apellidoinicialesM . $diferenciaDias . $sexoinicial;
                        $miembro->save();

                        //Calculo puntaje
                        $diff = $fechaactual->diff($fecha2);
                        $diferenciaaniosMiembroSegundario = $diff->y; //edad del miembro segundario de familia
                        //edad
                        $miembro->edad = $diferenciaaniosMiembroSegundario;
                        if ($diferenciaaniosMiembroSegundario >= 0 && $diferenciaaniosMiembroSegundario <= 17) {
                            $miembros_0_17_anios += 1;
                        } elseif ($diferenciaaniosMiembroSegundario >= 18 && $diferenciaaniosMiembroSegundario <= 59) {
                            $miembros_18_59_anios += 1;
                        } elseif ($diferenciaaniosMiembroSegundario >= 60) {
                            $miembros_mas_60_anios += 1;
                        }
                    }
                    //$encuesta->miembros  = $miembros; //miembros en encuesta
                    //validamos si hay miembros entre 18 y 59 para que no hay error al dividir por 0
                    if ($miembros_18_59_anios >= 1) {
                        $division1 = ($miembros_0_17_anios + $miembros_mas_60_anios) / ($miembros_18_59_anios);
                        $division = round($division1, 1);
                    } else {
                        $division = 0;
                    }

                    //return $division;
                    if ($division >= 0.7 && $division <= 1.2) {
                        $puntaje3 = 1;
                    } elseif ($division >= 1.3 && $division <= 1.8) {
                        $puntaje3 = 2;
                    } elseif ($division >= 1.9) {
                        $puntaje3 = 3;
                    }
                }
                $encuesta->puntaje_paso_tres = $puntaje3;
                //Calculo puntaje paso 4
                $puntaje_paso_cuatro = 0;
                if ($encuesta->mujeres_embarazadas == 1) {
                    $puntaje_paso_cuatro += 2;
                }
                if ($encuesta->mujeres_lactantes == 1) {
                    $puntaje_paso_cuatro += 2;
                }
                if ($encuesta->situacion_discapacidad == 1) {
                    $puntaje_paso_cuatro += 2;
                }
                if ($encuesta->enfermedades_cronicas == 1) {
                    $puntaje_paso_cuatro += 2;
                }
                $encuesta->puntaje_paso_cuatro = $puntaje_paso_cuatro;
                //calculo puntaje paso 5
                $puntaje_paso_cinco = 0;
                //para falta de comida
                if ($encuesta->falta_comida == 1) {
                    if ($encuesta->cuantas_veces_falta_comida == 'pocas_veces_1-2_veces') {
                        $puntaje_paso_cinco += 1;
                    } elseif ($encuesta->cuantas_veces_falta_comida == 'algunas_veces_3-10_veces') {
                        $puntaje_paso_cinco += 2;
                    } elseif ($encuesta->cuantas_veces_falta_comida == 'muchas_veces_mas_de_10_veces') {
                        $puntaje_paso_cinco += 3;
                    }
                }
                //para dormir sin comer
                if ($encuesta->dormir_sin_comer == 1) {
                    if ($encuesta->cuantas_veces_dormir_sin_comer == 'pocas_veces_1-2_veces') {
                        $puntaje_paso_cinco += 1;
                    } elseif ($encuesta->cuantas_veces_dormir_sin_comer == 'algunas_veces_3-10_veces') {
                        $puntaje_paso_cinco += 2;
                    } elseif ($encuesta->cuantas_veces_dormir_sin_comer == 'muchas_veces_mas_de_10_veces') {
                        $puntaje_paso_cinco += 3;
                    }
                }
                //para todo dia sin comer
                if ($encuesta->todo_dia_sin_comer == 1) {
                    if ($encuesta->cuantas_veces_todo_dia_sin_comer == 'pocas_veces_1-2_veces') {
                        $puntaje_paso_cinco += 1;
                    } elseif ($encuesta->cuantas_veces_todo_dia_sin_comer == 'algunas_veces_3-10_veces') {
                        $puntaje_paso_cinco += 2;
                    } elseif ($encuesta->cuantas_veces_todo_dia_sin_comer == 'muchas_veces_mas_de_10_veces') {
                        $puntaje_paso_cinco += 3;
                    }
                }
                $encuesta->puntaje_paso_cinco = $puntaje_paso_cinco;
                //calculo puntaje paso 6
                $puntaje_paso_seis = 0;
                if ($encuesta->satisfaccion_necesidades_basicas == 'todas') {
                    $puntaje_paso_seis += 0;
                } elseif ($encuesta->satisfaccion_necesidades_basicas == 'lamayoria') {
                    $puntaje_paso_seis += 1;
                } elseif ($encuesta->satisfaccion_necesidades_basicas == 'algunas') {
                    $puntaje_paso_seis += 2;
                } elseif ($encuesta->satisfaccion_necesidades_basicas == 'ninguna') {
                    $puntaje_paso_seis += 3;
                }

                if ($encuesta->satisfaccion_necesidades_basicas == 'algunas' || $encuesta->satisfaccion_necesidades_basicas == 'ninguna') {
                    if (sizeOf($encuesta['necesidadesbasicas']) > 0) {
                        foreach ($encuesta['necesidadesbasicas'] as $necesidad) {
                            if ($necesidad['id'] == 1 || $necesidad['id'] == 2 || $necesidad['id'] == 8) {
                                $puntaje_paso_seis += 1;
                            }
                        }
                    }
                }
                $encuesta->puntaje_paso_seis = $puntaje_paso_seis;
                //puntaje paso 7
                $puntaje_paso_siete = 0;
                if ($encuesta->tipo_vivienda_alojamiento_15_dias == 'inquilinato' || $encuesta->tipo_vivienda_alojamiento_15_dias == 'techo_improvisado') {
                    $puntaje_paso_siete += 1;
                } elseif ($encuesta->tipo_vivienda_alojamiento_15_dias == 'albergue_mas5dias') {
                    $puntaje_paso_siete += 2;
                } elseif ($encuesta->tipo_vivienda_alojamiento_15_dias == 'situacion_calle') {
                    $puntaje_paso_siete += 3;
                }
                $encuesta->puntaje_paso_siete = $puntaje_paso_siete;
                //puntaje paso 8
                $puntaje_paso_ocho = 0;
                if ($encuesta->gasto_hogar == 1) {
                    //al responder selecciono que NO hay gasto de hogar entonces valor es 1
                } elseif ($encuesta->gastos_percapita1) {
                    //selecciono que SI hay gasto de hogar e ingresa un valor entonces gasto_hogar es 0
                    if ($encuesta->gastos_percapita1 <= 29400) {
                        $puntaje_paso_ocho = 3;
                    } elseif ($encuesta->gastos_percapita1 >= 29401 && $encuesta->gastos_percapita1 <= 46900) {
                        $puntaje_paso_ocho = 2;
                    } elseif ($encuesta->gastos_percapita1 >= 46901 && $encuesta->gastos_percapita1 <= 64400) {
                        $puntaje_paso_ocho = 1;
                    } elseif ($encuesta->gastos_percapita1 >= 64401) {
                        $puntaje_paso_ocho = 0;
                    }
                    //encuesta->gasto_hogar = false;
                }
                $encuesta->puntaje_paso_ocho = $puntaje_paso_ocho;
                //Asignar el codigo_encuesta:
                $nombreiniciales = mb_strtoupper(mb_substr($encuesta->primer_nombre, 0, 2));
                $nombreiniciales2 = strtr($nombreiniciales, array('Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ñ' => 'N', 'Ü', 'U'));
                $nombreiniciales3 = str_replace('Ü', 'U', $nombreiniciales2);
                $apellidoiniciales = mb_strtoupper(mb_substr($encuesta->primer_apellido, 0, 2));
                $apellidoiniciales2 = strtr($apellidoiniciales, array('Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ñ' => 'N', 'Ü', 'U'));
                $apellidoiniciales3 = str_replace('Ü', 'U', $apellidoiniciales2);
                $fecha1900 = new DateTime("1900-01-01");
                $fecha2 = new DateTime($encuesta->fecha_nacimiento);
                $diff = $fecha1900->diff($fecha2);
                $diferenciaDias = $diff->days;
                $sexoinicial = strtoupper(substr($encuesta->sexo, 0, 1));
                $encuesta->codigo_encuesta = $nombreiniciales3 . $apellidoiniciales3 . $diferenciaDias . $sexoinicial;
                $encuesta->save();
            }
            return $encuestas;
        } catch (Exception $e) {
            return "error";
        }
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
                        //DATOS DE LLEGADA Y DESTINO
                        
                        $encuesta->como_llego_al_formulario = $request['infoencuesta']['comoLlegoAlFormularioCtrl'];

                        if($encuesta->como_llego_al_formulario == "Otro"){
                            $encuesta->donde_encontro_formulario = $request['infoencuesta']['dondeEncontroFormularioCtrl'];
                        }else{
                            $encuesta->donde_encontro_formulario = null;
                        }

                        $encuesta->fecha_llegada_pais = $request['infoencuesta']['llegadaDestinofechaLlegadaCtrl'];
                        $encuesta->estar_dentro_colombia = $request['infoencuesta']['llegadaDestinoPlaneaEstarEnColombiaCtrl']; //reciba 0-No, 1-Si, 2-NoEstoySeguro
            
                        if($encuesta->estar_dentro_colombia == 1 || $encuesta->estar_dentro_colombia == 2){
            
                            if($request['infoencuesta']['llegadaDestinoDepartamentoCtrl'] != 'nodefinido'){
                                
                                $encuesta->id_departamento_destino_final = $request['infoencuesta']['llegadaDestinoDepartamentoCtrl'];
                              
                                if($request['infoencuesta']['llegadaDestinoCiudadCtrl'] != 'nodefinido'){
                                    $encuesta->id_municipio_destino_final = $request['infoencuesta']['llegadaDestinoCiudadCtrl'];

                                }else{
                                    $encuesta->id_municipio_destino_final = null;
                                }
                            }else{
                                $encuesta->id_departamento_destino_final = null;
                                $encuesta->id_municipio_destino_final = null;
                            }

                            $encuesta->pais_destino_final = null;
                        
                        }else{
                            $encuesta->pais_destino_final = $request['infoencuesta']['llegadaDestinoDestinoFinalFueraColombiaCtrl'];

                            $encuesta->id_departamento_destino_final = null;
                             $encuesta->id_municipio_destino_final = null;

                        }
            
                        if ($encuesta->save()) {
                            return $encuesta->id;
                        } else {
                            return "error";
                        }



                    /*
                    case "paso1":
                        //DATOS DEL ENCUESTADO

                        $encuesta->primer_nombre = $request['infoencuesta']['firstNameCtrl'];
                        $encuesta->segundo_nombre = $request['infoencuesta']['secondNameCtrl'];
                        $encuesta->primer_apellido = $request['infoencuesta']['lastNameCtrl'];
                        $encuesta->segundo_apellido = $request['infoencuesta']['secondLastNameCtrl'];
                        $encuesta->sexo = $request['infoencuesta']['sexoCtrl'];
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
                        $nombreiniciales = mb_strtoupper(mb_substr($encuesta->primer_nombre, 0, 2));
                        $nombreiniciales2 = strtr($nombreiniciales, array('Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ñ' => 'N', 'Ü', 'U'));
                        $nombreiniciales3 = str_replace('Ü', 'U', $nombreiniciales2);
                        $apellidoiniciales = mb_strtoupper(mb_substr($encuesta->primer_apellido, 0, 2));
                        $apellidoiniciales2 = strtr($apellidoiniciales, array('Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ñ' => 'N', 'Ü', 'U'));
                        $apellidoiniciales3 = str_replace('Ü', 'U', $apellidoiniciales2);
                        $fecha1900 = new DateTime("1900-01-01");
                        $fecha2 = new DateTime($encuesta->fecha_nacimiento);
                        $diff = $fecha1900->diff($fecha2);
                        $diferenciaDias = $diff->days;
                        $sexoinicial = strtoupper(substr($encuesta->sexo, 0, 1));
                        $encuesta->codigo_encuesta = $nombreiniciales3 . $apellidoiniciales3 . $diferenciaDias . $sexoinicial;
                        if ($encuesta->save()) {
                            return $encuesta->id;
                        } else {
                            return "error";
                        }
                        break;
                    */
                   
                    case "paso2":

                        //miembros hogar

                        //calcular Ratio:
                        $miembros_0_17_anios = 0;
                        $miembros_18_59_anios = 0;
                        $miembros_mas_60_anios = 0;
                        //Calculo edad del miembro principal
                        $fecha1 = new DateTime();
                        $fecha2 = new DateTime($encuesta->fecha_nacimiento);
                        $diff = $fecha1->diff($fecha2);
                        $diferenciaaniosMiembroPrincipal = $diff->y;
                        if ($diferenciaaniosMiembroPrincipal >= 0 && $diferenciaaniosMiembroPrincipal <= 17) {
                            $miembros_0_17_anios += 1;
                        } elseif ($diferenciaaniosMiembroPrincipal >= 18 && $diferenciaaniosMiembroPrincipal <= 59) {
                            $miembros_18_59_anios += 1;
                        } elseif ($diferenciaaniosMiembroPrincipal >= 60) {
                            $miembros_mas_60_anios += 1;
                        }
                        //return $encuesta; //RETORNA ENCUESTA
                        $encuesta->paso = $request['paso'];
                        MiembrosHogar::where('id_encuesta', $encuesta->id)->delete();

                        $encuesta->total_miembros_hogar = $request['infoencuesta']['totalMiembrosHogar'];
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
                                    $addMiembro->fecha_nacimiento = date("Y-m-d", strtotime($miembro['fechaCtrl']));
                                    
                                    //nuevos campos
                                    $addMiembro->nacionalidad = $miembro['nacionalidadCtrl'];

                                    if($addMiembro->nacionalidad == 'Otro'){
                                        $addMiembro->cual_otro_nacionalidad = $miembro['otroNacionalidadCtrl'];
                                    }else{
                                        $addMiembro->cual_otro_nacionalidad = null;
                                    }


                                    $addMiembro->tipo_documento = $miembro['tipoDocumentoCtrl'];

                                    if($addMiembro->tipo_documento == 'Otro'){
                                        $addMiembro->cual_otro_tipo_documento = $miembro['otroTipoDocumentoCtrl'];
                                    }else{
                                        $addMiembro->cual_otro_tipo_documento = null;
                                    }


                                    //calculo edad de cada miembro de familia
                                    $fecha3 = new DateTime($addMiembro->fecha_nacimiento);
                                    $diff = $fecha1->diff($fecha3);
                                    $diferenciaaniosMiembroSegundario = $diff->y;
                                    if ($diferenciaaniosMiembroSegundario >= 0 && $diferenciaaniosMiembroSegundario <= 17) {
                                        $miembros_0_17_anios += 1;
                                    } elseif ($diferenciaaniosMiembroSegundario >= 18 && $diferenciaaniosMiembroSegundario <= 59) {
                                        $miembros_18_59_anios += 1;
                                    } elseif ($diferenciaaniosMiembroSegundario >= 60) {
                                        $miembros_mas_60_anios += 1;
                                    }
                                    //Asignar el codigo_encuesta:
                                    $nombreinicialesM = mb_strtoupper(mb_substr($miembro['primernombreCtrl'], 0, 2));
                                    $nombreinicialesM = strtr($nombreinicialesM, array('Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ñ' => 'N', 'Ü', 'U'));
                                    $nombreinicialesM = str_replace('Ü', 'U', $nombreinicialesM);
                                    $apellidoinicialesM = mb_strtoupper(mb_substr($miembro['primerapellidoCtrl'], 0, 2));
                                    $apellidoinicialesM = strtr($apellidoinicialesM, array('Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ñ' => 'N', 'Ü', 'U'));
                                    $apellidoinicialesM = str_replace('Ü', 'U', $apellidoinicialesM);
                                    $fecha2 = new DateTime(date("Y-m-d", strtotime($miembro['fechaCtrl'])));
                                    $fecha1900 = new DateTime("1900-01-01");
                                    $diffM = $fecha1900->diff($fecha2);
                                    $diferenciaDias = $diffM->days;
                                    $sexoinicial = strtoupper(substr($miembro['sexoCtrl'], 0, 1));
                                    $addMiembro->codigo_encuesta = $nombreinicialesM . $apellidoinicialesM . $diferenciaDias . $sexoinicial;
                                    
                                    
                                    //documento miembro
                                    if($addMiembro->tipo_documento == 'Indocumentado'){
                                        $addMiembro->numero_documento = null;
                                        $addMiembro->compartir_foto_documento = null;
                                    }else{
                                        $addMiembro->numero_documento = $miembro['numeroDocumentoCtrl'];

                                        $addMiembro->compartir_foto_documento = $miembro['compartirFotoDocumentoCtrl'];

                                        //si compartir foto documento es si
                                        if($addMiembro->compartir_foto_documento == 1 && $miembro['fotoDocumentoCtrl']){
                                            /*$foto = $request->json('base64textString');
                                            $nombre = $request->json('nombreArchivo');
                                            $archivo = base64_decode($foto);
                                            Storage::disk('local')->put($nombre, $archivo);*/

                                            $foto = $miembro['fotoDocumentoCtrl']['base64textString'];
                                           
                                            //$nombre = $miembro['fotoDocumentoCtrl']['nombreArchivo'];
                                            $formato = strstr($miembro['fotoDocumentoCtrl']['tipo'], '/');
                                            
                                            $replace = str_replace("/",".",$formato);

                                            //nombrearchivo y url : idencuesta-codigoencuesta.tipoarchivo
                                            $nombre = $encuesta->id . $addMiembro->codigo_encuesta . $replace; //codigo del miembro del hogar
                                            $archivo = base64_decode($foto);

                                            Storage::disk('local')->put($nombre, $archivo);

                                            $url = Storage::url($nombre);
                                           
                                            $addMiembro->url_foto_documento = $url;
                                            
                                        }else{  //si compartir foto documento es no, elimina los archivos que existan del miembro del hogar en todos los formatos

                                            $urleliminar1 = $encuesta->id . $addMiembro->codigo_encuesta . '.png';
                                            $urleliminar2 = $encuesta->id . $addMiembro->codigo_encuesta . '.jpg';
                                            $urleliminar3 = $encuesta->id . $addMiembro->codigo_encuesta . '.pdf';
                                            $urleliminar4 = $encuesta->id . $addMiembro->codigo_encuesta . '.jpeg';
                                            Storage::delete($urleliminar1);
                                            Storage::delete($urleliminar2);
                                            Storage::delete($urleliminar3);
                                            Storage::delete($urleliminar4);

                                                             
                                        }


                                    }
                                    
                                    
                                    if ($addMiembro->save()) {
                                        $guardado = true;
                                    }
                                }
                            }

                            $encuesta->unico_miembro_hogar = false;
                            //Calculo de Puntaje
                            if ($miembros_18_59_anios >= 1) {
                                $division1 = ($miembros_0_17_anios + $miembros_mas_60_anios) / ($miembros_18_59_anios);
                                $division = round($division1, 1);
                            } else {
                                $division = 0;
                            }
                            $puntaje = 0;
                            if ($division >= 0.7 && $division <= 1.2) {
                                $puntaje = 1;
                            } elseif ($division >= 1.3 && $division <= 1.8) {
                                $puntaje = 2;
                            } elseif ($division >= 1.9) {
                                $puntaje = 3;
                            }
                            $encuesta->puntaje_paso_tres = $puntaje;
                            if ($encuesta->gasto_hogar == 0) {
                                //Cuando SI hay gasto de hogar calcula nuevamente el gasto percapita
                                $gastos_percapita = $encuesta->total_gastos / ($miembros_0_17_anios + $miembros_mas_60_anios + $miembros_18_59_anios);
                                $encuesta->gastos_percapita1 = $gastos_percapita;
                            }
                            if ($encuesta->save() && $guardado == true) {
                                return ['encuesta' => $encuesta->id, 'Estado:' => 'Info Guardada'];
                            } else {
                                return ['encuesta' => $encuesta->id, 'Estado:' => 'Info NO Guardada'];
                            }
                        } else {
                            if ($encuesta->gasto_hogar == 0) {
                                $gastos_percapita = $encuesta->total_gastos / ($miembros_0_17_anios + $miembros_mas_60_anios + $miembros_18_59_anios);
                                $encuesta->gastos_percapita1 = $gastos_percapita;
                            }
                            $encuesta->unico_miembro_hogar = true;
                            $encuesta->puntaje_paso_tres = 0;
                            $encuesta->save();
                            return ['encuesta' => $encuesta->id, 'Estado:' => 'Sin otros miembros de Hogar'];
                        }
                        break;

                     case "paso3":
                        //DATOS DE CONTACTO
                        if (/*$request['infoencuesta']['departamentoCtrl'] != "" && $request['infoencuesta']['municipioCtrl'] != ""
                            && $request['infoencuesta']['barrioCtrl'] != "" &&*/ $request['infoencuesta']['numeroContactoCtrl'] != ""
                            && $request['infoencuesta']['lineaContactoPropiaCtrl'] != "") {
                            $encuesta->paso = $request['paso'];
                            //$encuesta->id_departamento = $request['infoencuesta']['departamentoCtrl'];
                           // $encuesta->id_municipio = $request['infoencuesta']['municipioCtrl'];

                           //si selecciono municipio ubicacionCtrl

                           
                           if(isset($request['infoencuesta']['ubicacionCtrl']['nombre'])){

                                $encuesta->ubicacion = $request['infoencuesta']['ubicacionCtrl']['nombre'];
                           }else{
                                $encuesta->ubicacion = $request['infoencuesta']['ubicacionCtrl'];
                            }


                                //si selecciono Otro municipio
                            /*if($request['infoencuesta']['ubicacionCtrl']['nombre'] === "Otro"){

                                    $encuesta->ubicacion = $request['infoencuesta']['ubicacionOtroCtrl'];
        
                                }else{ //si selecciono municipio 
                                    $encuesta->ubicacion = $request['infoencuesta']['ubicacionCtrl']['nombre'];
                                }

                           }else{ //si ingreso nombre nuevo municipio

                            //return $request['infoencuesta']['nuevoMunicipioUbicacionCtrl'];
                            $encuesta->ubicacion = $request['infoencuesta']['nuevoMunicipioUbicacionCtrl'];

                           }
                           */
                          
                           
                            //$encuesta->barrio = $request['infoencuesta']['barrioCtrl'];
                            //$encuesta->direccion = $request['infoencuesta']['direccionCtrl'];
                            $encuesta->numero_contacto = $request['infoencuesta']['numeroContactoCtrl'];

                            //linea de contacto principal es propia?
                            if ($request['infoencuesta']['lineaContactoPropiaCtrl'] === 'si') {
                                $encuesta->linea_contacto_propia = 1;

                                /*if ($request['infoencuesta']['lineaContactoAsociadaAWhatsappCtrl'] == 'si') {
                                    $encuesta->linea_asociada_whatsapp = 1;
                                    $encuesta->numero_whatsapp_principal = null;


                                } else if ($request['infoencuesta']['lineaContactoAsociadaAWhatsappCtrl'] == 'no') {
                                    $encuesta->linea_asociada_whatsapp = 0;
                                    $encuesta->numero_whatsapp_principal = $request['infoencuesta']['numeroWhatsappCtrl'];

                                }*/

                                //$encuesta->numero_alternativo = null;
                                //$encuesta->linea_contacto_alternativo = null;
                                //$encuesta->linea_alternativa_asociada_whatsapp = null;

                            } else if ($request['infoencuesta']['lineaContactoPropiaCtrl'] === "no") {
                                $encuesta->linea_contacto_propia = 0;
                                //$encuesta->linea_asociada_whatsapp = 0;
                                
                                //$encuesta->preguntar_en_caso_de_llamar = $request['infoencuesta']['contactoAlternativoCtrl'];

                                /*$encuesta->numero_alternativo = $request['infoencuesta']['contactoAlternativoCtrl'];

                                if($request['infoencuesta']['lineaContactoAlternativoCtrl'] === 'si'){
                                    $encuesta->linea_contacto_alternativo = 1;

                                    if($request['infoencuesta']['lineaContactoAlternativoAsociadaAWhatsappCtrl'] == 'si'){
                                        $encuesta->linea_alternativa_asociada_whatsapp = 1; 
                                    }else if($request['infoencuesta']['lineaContactoAlternativoAsociadaAWhatsappCtrl'] == 'no'){
                                        $encuesta->linea_alternativa_asociada_whatsapp = 0; 
                                    }
                                }else if($request['infoencuesta']['lineaContactoAlternativoCtrl'] === 'no'){
                                    $encuesta->linea_contacto_alternativo = 0;
                                    $encuesta->linea_alternativa_asociada_whatsapp = 0;
                                }*/
                            }

                            //linea de contacto principal esta asociada a whatsapp?
                            if ($request['infoencuesta']['lineaContactoAsociadaAWhatsappCtrl'] == 'si') {
                                    $encuesta->linea_asociada_whatsapp = 1;
                                    $encuesta->numero_whatsapp_principal = null;
                            } else if ($request['infoencuesta']['lineaContactoAsociadaAWhatsappCtrl'] == 'no') {
                                    $encuesta->linea_asociada_whatsapp = 0;
                                    $encuesta->numero_whatsapp_principal = $request['infoencuesta']['numeroWhatsappCtrl'];
                            }


                            $encuesta->numero_alternativo = $request['infoencuesta']['contactoAlternativoCtrl'];

                            //linea de contacto alternativo es propia?
                            if($request['infoencuesta']['lineaContactoAlternativoCtrl'] === 'si'){
                                    $encuesta->linea_contacto_alternativo = 1;

                                    if($request['infoencuesta']['lineaContactoAlternativoAsociadaAWhatsappCtrl'] == 'si'){
                                        $encuesta->linea_alternativa_asociada_whatsapp = 1; 
                                    }else if($request['infoencuesta']['lineaContactoAlternativoAsociadaAWhatsappCtrl'] == 'no'){
                                        $encuesta->linea_alternativa_asociada_whatsapp = 0; 
                                    }
                            }else if($request['infoencuesta']['lineaContactoAlternativoCtrl'] === 'no'){
                                    $encuesta->linea_contacto_alternativo = 0;
                                    //$encuesta->linea_alternativa_asociada_whatsapp = 0;
                            }
                            

                            $encuesta->correo_electronico = $request['infoencuesta']['correoCtrl'];
                            

                            if($request['infoencuesta']['tieneCuentaFacebook'] == 'si'){
                                $encuesta->tiene_cuenta_facebook = 1;
                                $encuesta->cuenta_facebook = $request['infoencuesta']['cuentaFacebookCtrl'];

                            }else if($request['infoencuesta']['tieneCuentaFacebook'] == 'no'){
                                $encuesta->tiene_cuenta_facebook = 0;
                                $encuesta->cuenta_facebook = null;
                            }


                            //si podemos contactarte
                            if($request['infoencuesta']['podemosContactarte'] == 'si'){
                                $encuesta->podemos_contactarte = 1;
                                $encuesta->forma_contactarte = $request['infoencuesta']['formaContactarteCtrl'];

                                if($encuesta->forma_contactarte == 'Otro'){
                                    $encuesta->otra_forma_contactarte = $request['infoencuesta']['otraFormaContactarteCtrl'];
                                }else{
                                    $encuesta->otra_forma_contactarte = null;
                                }
                            }else{ //no podemos contactarte
                                $encuesta->podemos_contactarte = 0;
                                $encuesta->forma_contactarte = null;
                                $encuesta->otra_forma_contactarte = null;
                            }


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

                    case "paso4":
                        $puntaje_paso_cuatro = 0;
                        $encuesta->paso = $request['paso'];
                        $encuesta->mujeres_embarazadas = $request['infoencuesta']['mujeresEmbarazadasCtrl'];
                        //calculo puntaje
                        if ($encuesta->mujeres_embarazadas && $encuesta->mujeres_embarazadas == 1) {
                            $puntaje_paso_cuatro += 2;
                        }

                        $encuesta->mujeres_lactantes = $request['infoencuesta']['mujeresLactantesCtrl'];
                        if ($encuesta->mujeres_lactantes && $encuesta->mujeres_lactantes == 1) {
                            $puntaje_paso_cuatro += 2;
                        }
                        $encuesta->situacion_discapacidad = $request['infoencuesta']['personasDiscapacidadCtrl'];
                        if ($encuesta->situacion_discapacidad && $encuesta->situacion_discapacidad == 1) {
                            $puntaje_paso_cuatro += 2;
                        }
                        $encuesta->enfermedades_cronicas = $request['infoencuesta']['personasEnfermedadesCronicasCtrl'];
                        if ($encuesta->enfermedades_cronicas && $encuesta->enfermedades_cronicas == 1) {
                            $puntaje_paso_cuatro += 2;
                        }
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
                        if ($encuesta->falta_comida == 1) {
                            $encuesta->cuantas_veces_falta_comida = $request['infoencuesta']['alimentos12Ctrl'];
                            if ($encuesta->cuantas_veces_falta_comida == 'pocas_veces_1-2_veces') {
                                $puntaje_paso_cinco += 1;
                            } elseif ($encuesta->cuantas_veces_falta_comida == 'algunas_veces_3-10_veces') {
                                $puntaje_paso_cinco += 2;
                            } elseif ($encuesta->cuantas_veces_falta_comida == 'muchas_veces_mas_de_10_veces') {
                                $puntaje_paso_cinco += 3;
                            }
                        } else {
                            $encuesta->cuantas_veces_falta_comida = null;
                        }

                        //para dormir sin comer
                        $encuesta->dormir_sin_comer = $request['infoencuesta']['alimentos13Ctrl'];
                        if ($encuesta->dormir_sin_comer == 1) {
                            $encuesta->cuantas_veces_dormir_sin_comer = $request['infoencuesta']['alimentos14Ctrl'];
                            if ($encuesta->cuantas_veces_dormir_sin_comer == 'pocas_veces_1-2_veces') {
                                $puntaje_paso_cinco += 1;
                            } elseif ($encuesta->cuantas_veces_dormir_sin_comer == 'algunas_veces_3-10_veces') {
                                $puntaje_paso_cinco += 2;
                            } elseif ($encuesta->cuantas_veces_dormir_sin_comer == 'muchas_veces_mas_de_10_veces') {
                                $puntaje_paso_cinco += 3;
                            }
                        } else {
                            $encuesta->cuantas_veces_dormir_sin_comer = null;
                        }
                        //para todo dia sin comer
                        $encuesta->todo_dia_sin_comer = $request['infoencuesta']['alimentos15Ctrl'];
                        if ($encuesta->todo_dia_sin_comer == 1) {
                            $encuesta->cuantas_veces_todo_dia_sin_comer = $request['infoencuesta']['alimentos16Ctrl'];
                            if ($encuesta->cuantas_veces_todo_dia_sin_comer == 'pocas_veces_1-2_veces') {
                                $puntaje_paso_cinco += 1;
                            } elseif ($encuesta->cuantas_veces_todo_dia_sin_comer == 'algunas_veces_3-10_veces') {
                                $puntaje_paso_cinco += 2;
                            } elseif ($encuesta->cuantas_veces_todo_dia_sin_comer == 'muchas_veces_mas_de_10_veces') {
                                $puntaje_paso_cinco += 3;
                            }
                        } else {
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
                            if ($encuesta->satisfaccion_necesidades_basicas == 'todas') {
                                $puntaje_paso_seis += 0;
                            } elseif ($encuesta->satisfaccion_necesidades_basicas == 'lamayoria') {
                                $puntaje_paso_seis += 1;
                            } elseif ($encuesta->satisfaccion_necesidades_basicas == 'algunas') {
                                $puntaje_paso_seis += 2;
                            } elseif ($encuesta->satisfaccion_necesidades_basicas == 'ninguna') {
                                $puntaje_paso_seis += 3;
                            }
                            $encuesta->necesidadesbasicas()->detach();
                            if ($encuesta->satisfaccion_necesidades_basicas == 'algunas' || $encuesta->satisfaccion_necesidades_basicas == 'ninguna') {
                                if (sizeOf($request['infoencuesta']['necesidades22Ctrl']) > 0) {
                                    foreach ($request['infoencuesta']['necesidades22Ctrl'] as $necesidad) {
                                        $encuesta->necesidadesbasicas()->attach(intval($necesidad));

                                        if (intval($necesidad) == 1 || intval($necesidad) == 2 || intval($necesidad) == 8) {
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
                        if ($encuesta->tipo_vivienda_alojamiento_15_dias == 'inquilinato' || $encuesta->tipo_vivienda_alojamiento_15_dias == 'techo_improvisado') {
                            $puntaje_paso_siete += 1;
                        } elseif ($encuesta->tipo_vivienda_alojamiento_15_dias == 'albergue_mas5dias') {
                            $puntaje_paso_siete += 2;
                        } elseif ($encuesta->tipo_vivienda_alojamiento_15_dias == 'situacion_calle') {
                            $puntaje_paso_siete += 3;
                        }
                        $encuesta->puntaje_paso_siete = $puntaje_paso_siete;
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
                            if ($encuesta->gastos_percapita1 <= 29400) {
                                $puntaje_paso_ocho = 3;
                            } elseif ($encuesta->gastos_percapita1 >= 29401 && $encuesta->gastos_percapita1 <= 46900) {
                                $puntaje_paso_ocho = 2;
                            } elseif ($encuesta->gastos_percapita1 >= 46901 && $encuesta->gastos_percapita1 <= 64400) {
                                $puntaje_paso_ocho = 1;
                            }

                            $encuesta->gasto_hogar = false;
                        }
                        $encuesta->puntaje_paso_ocho = $puntaje_paso_ocho;
                        $encuesta->save();
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



    /*
    public function actualizardatos(Request $request){


        try {
            $miembroHogar = MiembrosHogar::with(['encuesta'])
            ->where('numero_documento',$request['numeroDocumentoCtrl'])
            ->where('tipo_documento',$request['tipoDocumentoCtrl'])
            ->first();

            //$miembroHogar->tipo_documento = $request['tipoDocumentoCtrl'];
            //$miembroHogar-> = $request['telefonoCtrl']

            $encuesta = Encuesta::where('id', '=', $miembroHogar['encuesta']['id'])->first();

            $encuesta['tipo_documento'] = $request['tipoDocumentoCtrl'];
            $encuesta['numero_documento'] = $request['numeroDocumentoCtrl'];
            $encuesta['numero_contacto'] = $request['telefonoCtrl'];
            $encuesta['correo_electronico'] = $request['correoCtrl'];
            

            
            if ($encuesta->save()) {
                return response()->json([
                    'res' => $encuesta->id,
                    //'token' => $token,
                    'message' => 'Encuesta Actualizada'
                ],200);
            } else {
                return "error";
            }
        } catch (\Throwable $e) {
            return "error";
        }

        
    }

    
    public function reportarllegada(Request $request){

        try {
            $miembroHogar = MiembrosHogar::with(['encuesta'])
            ->where('numero_documento','=',$request['formData']['numeroDocumentoCtrl'])
            ->where('tipo_documento','=',$request['formData']['tipoDocumentoCtrl'])
            ->first();

            //$miembroHogar->tipo_documento = $request['tipoDocumentoCtrl'];
            //$miembroHogar-> = $request['telefonoCtrl']

            

            $encuesta = Encuesta::where('id', '=', $miembroHogar['encuesta']['id'])->first();

            $encuesta['tipo_documento'] = $request['formData']['tipoDocumentoCtrl'];
            $encuesta['id_departamento'] = $request['formData']['departamentoCtrl'];
            $encuesta['id_municipio'] = $request['formData']['municipioCtrl'];
            $encuesta['numero_contacto'] = $request['formData']['telefonoCtrl'];
            $encuesta['numero_documento'] = $request['formData']['numeroDocumentoCtrl'];

            

            $autorizacion = Autorizacion::where('id_encuesta','=',$encuesta['id'])->first();

            $autorizacion->latitud = $request['coordenadas']['latitud'];
            $autorizacion->longitud = $request['coordenadas']['longitud'];

       
            if ($encuesta->save() && $autorizacion->save()) {
                return response()->json([
                    'res' => $encuesta->id,
                    //'token' => $token,
                    'message' => 'Encuesta Actualizada'
                ],200);
            } else {
                return "error";
            }
        } catch (\Throwable $e) {
            return "error";
        }
    }*/
}
