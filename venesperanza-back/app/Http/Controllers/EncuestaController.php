<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Encuesta;




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

       


        $ben = new Encuesta;

        
        $ben->paso = $request['paso'];
        $ben->primer_nombre =  $request['infoencuesta']['firstNameCtrl'];;
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

        if($ben){
            return $ben;
        }else{
            return error;
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
        
        $encuesta = Encuesta::find($id);

        

        if($encuesta){

            print($request['paso']);

            if($request['paso'] == "paso1"){

                $encuesta->primer_nombre =  $request['infoencuesta']['firstNameCtrl'];;
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
                
                
                $encuesta->save();

            }else if($request['paso'] == "paso2"){
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
                }




            }

            
        }else{
            return "ERRORNO ENCONTRO ENCUESTA";

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
