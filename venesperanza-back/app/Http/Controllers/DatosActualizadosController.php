<?php

namespace App\Http\Controllers;

use App\Models\DatosActualizados;
use App\Models\Encuesta;
use App\Models\MiembrosHogar;
use App\Models\Autorizacion;
use App\Models\Intentos;

use Illuminate\Http\Request;

class DatosActualizadosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function actualizardatos(Request $request){


        try {
            /*
            $miembroHogar = MiembrosHogar::with(['encuesta'])
            ->where('numero_documento',$request['numeroDocumentoCtrl'])
            ->where('tipo_documento',$request['tipoDocumentoCtrl'])
            ->first();*/

            $encuesta = Encuesta::where('numero_documento', $request['numeroDocumentoCtrl'])
            ->where('tipo_documento', $request['tipoDocumentoCtrl'])
            ->first();


            $datosGuardados = null;
            //if($miembroHogar){
            if($encuesta){

                $datosActualizados = new DatosActualizados;

                $datosActualizados->tipo_documento = $request['tipoDocumentoCtrl'];
                $datosActualizados->numero_documento = $request['numeroDocumentoCtrl'];
                $datosActualizados->telefono = $request['telefonoCtrl'];
                $datosActualizados->correo_electronico = $request['correoCtrl'];
                //$datosActualizados->id_encuesta = $miembroHogar['encuesta']['id'];
                $datosActualizados->id_encuesta = $encuesta['id'];

                $datosActualizados->save();

                $encuesta->numero_contacto = $request['telefonoCtrl'];
                $encuesta->correo_electronico = $request['correoCtrl'];
                $encuesta->save();


                $datosGuardados = $datosActualizados;
                
            }else{
                $intentos = new Intentos;

                $intentos->tipo_documento = $request['tipoDocumentoCtrl'];
                $intentos->numero_documento = $request['numeroDocumentoCtrl'];
                $intentos->numero_contacto = $request['telefonoCtrl']; 
                $intentos->correo_electronico = $request['correoCtrl'];

                $intentos->save();

                $datosGuardados = $intentos;
            }

            
            
            
            if ($datosGuardados) {
                return response()->json([
                    'res' => $datosGuardados->id,
                    //'token' => $token,
                    'message' => 'Datos Actualizados'
                ],200);
            } else {
                return "error";
            }
        } catch (\Throwable $e) {
            return $e;
        }

        
    }

   


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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DatosActualizados  $datosActualizados
     * @return \Illuminate\Http\Response
     */
    public function show(DatosActualizados $datosActualizados)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DatosActualizados  $datosActualizados
     * @return \Illuminate\Http\Response
     */
    public function edit(DatosActualizados $datosActualizados)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DatosActualizados  $datosActualizados
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DatosActualizados $datosActualizados)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DatosActualizados  $datosActualizados
     * @return \Illuminate\Http\Response
     */
    public function destroy(DatosActualizados $datosActualizados)
    {
        //
    }
}
