<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Encuesta;
use App\Models\MiembrosHogar;




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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

       
        try {
            //code...
        

        $ben = new Encuesta;

        
        $ben->paso = $request['paso'];
        $ben->primer_nombre =  $request['infoencuesta']['firstNameCtrl'];
        $ben->segundo_nombre =  $request['infoencuesta']['secondNameCtrl'];
        $ben->primer_apellido =  $request['infoencuesta']['lastNameCtrl'];
        $ben->segundo_apellido = $request['infoencuesta']['secondLastNameCtrl'];
        $ben->sexo =  $request['infoencuesta']['sexoCtrl'];
        $ben->fecha_nacimiento = date("d/m/Y", strtotime($request['infoencuesta']['fechaNacimientoCtrl']));
        //$ben->fecha_nacimiento =  $request['infoencuesta']['fechaNacimientoCtrl'];
        $ben->nacionalidad =  $request['infoencuesta']['nacionalidadCtrl'];
        $ben->tipo_documento =  $request['infoencuesta']['tipoDocumentoCtrl'];

        if($ben->tipo_documento == "Otro"){
            $ben->cual_otro_tipo_documento = $request['infoencuesta']['otroTipoDocumentoCtrl'];
        }

        if($ben->tipo_documento != "Indocumentado"){
            $ben->numero_documento =$request['infoencuesta']['numeroDocumentoCtrl'];
        }
        
        
        $ben->save();
        
        return $ben;


        } catch (Exception $e) {
            return $e;
            //throw $th;
        }

       

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        try {
                    //code...
                
                
                $encuesta = Encuesta::find($id);

                

                if($encuesta){

                    switch ($request['paso']) {
                        case "paso1":
                            
                            $encuesta->primer_nombre =  $request['infoencuesta']['firstNameCtrl'];
                            $encuesta->segundo_nombre =  $request['infoencuesta']['secondNameCtrl'];
                            $encuesta->primer_apellido =  $request['infoencuesta']['lastNameCtrl'];
                            $encuesta->segundo_apellido = $request['infoencuesta']['secondLastNameCtrl'];
                            $encuesta->sexo =  $request['infoencuesta']['sexoCtrl'];
                            $encuesta->fecha_nacimiento = date("d/m/Y", strtotime($request['infoencuesta']['fechaNacimientoCtrl']));
                            //$ben->fecha_nacimiento =  $request['infoencuesta']['fechaNacimientoCtrl'];
                            $encuesta->nacionalidad =  $request['infoencuesta']['nacionalidadCtrl'];
                            $encuesta->tipo_documento =  $request['infoencuesta']['tipoDocumentoCtrl'];

                            if($encuesta->tipo_documento == "Otro"){
                                $encuesta->cual_otro_tipo_documento = $request['infoencuesta']['otroTipoDocumentoCtrl'];
                            }

                            if($encuesta->tipo_documento != "Indocumentado"){
                                $encuesta->numero_documento =$request['infoencuesta']['numeroDocumentoCtrl'];
                            }
                            
                            
                            if($encuesta->save()){
                                return $encuesta;
                            }else{
                                return error;
                            };
                            break;
                        
                        case "paso2":

                            if($request['infoencuesta']['departamentoCtrl'] != "" && $request['infoencuesta']['municipioCtrl'] != ""
                            && $request['infoencuesta']['barrioCtrl'] != "" && $request['infoencuesta']['numeroContactoCtrl'] != ""
                            && $request['infoencuesta']['lineaContactoPropiaCtrl'] != "" )
                            {
                                    $encuesta->paso = $request['paso'];
                                    $encuesta->id_departamento =  $request['infoencuesta']['departamentoCtrl'];
                                    $encuesta->id_municipio =  $request['infoencuesta']['municipioCtrl'];
                                    $encuesta->barrio = $request['infoencuesta']['barrioCtrl'];
                                    $encuesta->direccion = $request['infoencuesta']['direccionCtrl'];
                                    $encuesta->numero_contacto = $request['infoencuesta']['numeroContactoCtrl'];

                                    if($request['infoencuesta']['lineaContactoPropiaCtrl'] === 'si'){
                                        $encuesta->linea_contacto_propia = 1;

                                        if($request['infoencuesta']['lineaContactoAsociadaAWhatsappCtrl'] == 'si'){
                                            $encuesta->linea_asociada_whatsapp = 1;

                                        }else if($request['infoencuesta']['lineaContactoAsociadaAWhatsappCtrl'] == 'no'){
                                            $encuesta->linea_asociada_whatsapp = 0; 
                                        }

                                    }else if($request['infoencuesta']['lineaContactoPropiaCtrl'] === "no"){
                                        $encuesta->linea_contacto_propia = 0;
                                        $encuesta->linea_asociada_whatsapp = 0; 

                                        $encuesta->preguntar_en_caso_de_llamar = $request['infoencuesta']['contactoAlternativoCtrl'];

                                    }
                                    
                                    $encuesta->correo_electronico = $request['infoencuesta']['correoCtrl'];
                                    $encuesta->comentario = $request['infoencuesta']['comentarioAdicionalCtrl'];


                                    $encuesta->save();

                                    if($encuesta){
                                        return $encuesta;
                                    }else{
                                        return error;
                                    };
                            }else{
                                return error;
                            }

                            break;

                    
                        
                        case "paso3":

                            $encuesta->paso = $request['paso'];

                            $miembrosexistentes = MiembrosHogar::where('id_encuesta',$encuesta->id)->delete();
                        
                            
                            if(count($request['infoencuesta']['miembrosFamilia'])>0){

                                $guardado = false;

                                foreach ( $request['infoencuesta']['miembrosFamilia'] as $miembro) {

                                    $addMiembro = new MiembrosHogar;

                                    if($addMiembro){

                                        $addMiembro->id_encuesta = $encuesta->id;
                                        $addMiembro->primer_nombre_miembro = $miembro['primernombreCtrl'];
                                        $addMiembro->segundo_nombre_miembro = $miembro['segundonombreCtrl'];
                                        $addMiembro->primer_apellido_miembro = $miembro['primerapellidoCtrl'];
                                        $addMiembro->segundo_apellido_miembro = $miembro['segundoapellidoCtrl'];
                                        $addMiembro->sexo_miembro = $miembro['sexoCtrl'];
                                        $addMiembro->fecha_nacimiento = date("d/m/Y", strtotime($miembro['fechaCtrl']));

                                        
                                        if($addMiembro->save()){
                                            $guardado = true;
                                        }

                                    }

                                    
                                }

                                if($encuesta->save() && $guardado == true){
                                    return ['encuesta'=>$encuesta,'Estado:'=>'Info Guardada'];
                                }else{
                                    return ['encuesta'=>$encuesta,'Estado:'=>'Info NO Guardada'];
                                }

                            }else{
                                $encuesta->save();
                                return ['encuesta'=>$encuesta,'Estado:'=>'Sin otros miembros de Hogar'];
                            }

                            
                            

                            
                            


                            break;
                        
                        case "paso4":

                            $encuesta->paso = $request['paso'];

                            $encuesta->mujeres_embarazadas = $request['infoencuesta']['mujeresEmbarazadasCtrl'];
                            $encuesta->mujeres_lactantes =  $request['infoencuesta']['mujeresLactantesCtrl'];
                            $encuesta->situacion_discapacidad =  $request['infoencuesta']['personasDiscapacidadCtrl'];
                            $encuesta->enfermedades_cronicas =  $request['infoencuesta']['personasEnfermedadesCronicasCtrl'];

                            $encuesta->save();

                            if($encuesta){
                                return $encuesta;
                            }else{
                                return error;
                            }

                            break;
                        
                        case "paso5":

                            $encuesta->paso = $request['paso'];
                            $encuesta->falta_comida = $request['infoencuesta']['alimentos11Ctrl'];
                            $encuesta->cuantas_veces_falta_comida = $request['infoencuesta']['alimentos12Ctrl'];
                            $encuesta->dormir_sin_comer = $request['infoencuesta']['alimentos13Ctrl'];
                            $encuesta->cuantas_veces_dormir_sin_comer = $request['infoencuesta']['alimentos14Ctrl'];
                            $encuesta->todo_dia_sin_comer = $request['infoencuesta']['alimentos15Ctrl'];
                            $encuesta->cuantas_veces_todo_dia_sin_comer = $request['infoencuesta']['alimentos16Ctrl'];

                            $encuesta->save();


                            if($encuesta){
                                return $encuesta;
                            }else{
                                return "error";
                            }
                            
                            break;
                        
                        case "paso6":

                            if($request['infoencuesta']['necesidades17Ctrl'] != "" && sizeOf($request['infoencuesta']['necesidades22Ctrl']) > 0 ){

                                $encuesta->paso = $request['paso'];
                                $encuesta->satisfaccion_necesidades_basicas = $request['infoencuesta']['necesidades17Ctrl'];
                                //$encuesta->cuantas_veces_falta_comida = $request['infoencuesta']['necesidades22Ctrl'];

                                $encuesta->necesidadesbasicas()->detach();

                                foreach ($request['infoencuesta']['necesidades22Ctrl'] as $necesidad) {
                                    $encuesta->necesidadesbasicas()->attach(intval($necesidad));
                        
                                }


                                $encuesta->save();


                                if($encuesta){
                                    return $encuesta;
                                }else{
                                    return error;
                                }
                            }else{
                                return error;
                            }



                            break;
                        
                        case "paso7":

                            $encuesta->paso = $request['paso'];
                            $encuesta->tipo_vivienda_alojamiento_15_dias = $request['infoencuesta']['alojamientoViviendaCtrl'];

                            $encuesta->save();

                            if($encuesta){
                                return $encuesta;
                            }else{
                                return "error";
                            }

                        case "paso8":

                            $encuesta->paso = $request['paso'];
                            $encuesta->ingresos_c = $request['infoencuesta']['economicoCtrl'];
                            $encuesta->total_gastos = $request['infoencuesta']['gastoHogar7diasCtrl'];
                            $encuesta->gastos_percapita1 = $encuesta->total_gastos / $request['infoencuesta']['cantidad_miembros'];

                            $encuesta->save();
            
                            if($encuesta){
                                    return $encuesta;
                            }else{
                                    return "error";
                            }
                        default:
                            # code...
                            break;
                    }

                    
                }else{
                    return error;
                }

            } catch (Exception $e) {
                return error;
            }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
