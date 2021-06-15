<?php

namespace App\Http\Controllers;

use App\Models\Llegadas;
use App\Models\Encuesta;
use App\Models\MiembrosHogar;
use App\Models\Intentos;

use Illuminate\Http\Request;

class LlegadasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function reportarllegada(Request $request)
    {

        try {
            /*$miembroHogar = MiembrosHogar::with(['encuesta'])
                ->where('numero_documento', '=', $request['formData']['numeroDocumentoCtrl'])
                ->where('tipo_documento', '=', $request['formData']['tipoDocumentoCtrl'])
                ->first();*/
            
            $encuesta = Encuesta::where('numero_documento', '=', $request['formData']['numeroDocumentoCtrl'])
            ->where('tipo_documento', '=', $request['formData']['tipoDocumentoCtrl'])
            ->first();


            $datosGuardados = null;
            //if ($miembroHogar) {
            if($encuesta){

                $llegada = new Llegadas;
                $llegada->tipo_documento = $request['formData']['tipoDocumentoCtrl'];
                $llegada->numero_documento = $request['formData']['numeroDocumentoCtrl'];
                $llegada->id_departamento = $request['formData']['departamentoCtrl'];
                $llegada->id_municipio = $request['formData']['municipioCtrl'];
                $llegada->telefono = $request['formData']['telefonoCtrl'];
                //$llegada->id_encuesta = $miembroHogar['encuesta']['id'];
                $llegada->id_encuesta = $encuesta['id'];

                if ($request['coordenadas']) {
                    $llegada->latitud = $request['coordenadas']['latitud'];
                    $llegada->longitud = $request['coordenadas']['longitud'];
                }
                $llegada->save();


                $datosGuardados = $llegada;
            } else {
                $intentos = new Intentos;

                $intentos->tipo_documento = $request['formData']['tipoDocumentoCtrl'];
                $intentos->numero_documento = $request['formData']['numeroDocumentoCtrl'];
                $intentos->id_departamento = $request['formData']['departamentoCtrl'];
                $intentos->id_municipio = $request['formData']['municipioCtrl'];
                $intentos->telefono = $request['formData']['telefonoCtrl'];
                if ($request['coordenadas']) {
                    $intentos->latitud = $request['coordenadas']['latitud'];
                    $intentos->longitud = $request['coordenadas']['longitud'];
                }

                $intentos->save();

                $datosGuardados = $intentos;
            }
            if ($datosGuardados) {
                return response()->json([
                    'res' => $datosGuardados->id,
                    //'token' => $token,
                    'message' => 'Llegada guardada'
                ], 200);
            } else {
                return "error";
            }
        } catch (\Throwable $e) {
            return "error";
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Llegadas $llegadas
     * @return \Illuminate\Http\Response
     */
    public function show(Llegadas $llegadas)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Llegadas $llegadas
     * @return \Illuminate\Http\Response
     */
    public function edit(Llegadas $llegadas)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Llegadas $llegadas
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Llegadas $llegadas)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Llegadas $llegadas
     * @return \Illuminate\Http\Response
     */
    public function destroy(Llegadas $llegadas)
    {
        //
    }
}
