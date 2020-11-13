<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\DB;

use App\Models\Departamento;

use App\Models\Municipio;
use App\Models\Barrio;

use App\Models\Autorizacion;


class MunicipioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Municipio::all();
    }

    public function obtenerBarrios()
    {
        return Barrio::all();
    }

    public function validarUbicacionEnVRosario(Request $request)
    {
        $arrayLongitudes = [];
        $arrayLatitudes = [];
        try {
            if (env('MODE') === "Test") {
                return ['valid' => true];
            }
            $posicionenpoligono = false;
            if ($request['adf'] == env('APP_KEY_ADF') && strpos($request['ref'], 'facebook.com') !== false) {
                $ipvalidar = $_SERVER['REMOTE_ADDR'];
                $autorizacion = DB::table('autorizaciones')->where('ip', $ipvalidar)->first();
                if ($autorizacion) {
                    return ['valid' => $posicionenpoligono];
                } else {
                    $jsonfile = Storage::get('public/villaDelRosarioGeoJSON.json');
                    $coordenadas = json_decode($jsonfile, true);
                    foreach ($coordenadas as $key => $coordenada) {
                        array_push($arrayLatitudes, $coordenada['geometry']['coordinates'][0]);
                        array_push($arrayLongitudes, $coordenada['geometry']['coordinates'][1]);
                    }
                    $points_polygon = count($arrayLongitudes) - 1; //numero de vertices
                    $posicionenpoligono = $this->is_in_polygon($points_polygon, $arrayLongitudes,
                        $arrayLatitudes, $request['coordenadas']['longitud'],
                        $request['coordenadas']['latitud']);
                    return ['valid' => $posicionenpoligono];
                }
            } else {
                return ['valid' => $posicionenpoligono];
            }
        } catch (Exception $e) {
            return $e;
        }
    }

    //Valido si coordenadas de ubicacion están dentro del poligono
    function is_in_polygon($points_polygon, $vertices_x, $vertices_y, $latitude_y, $longitude_x)
        //function is_in_polygon($longitude_x, $latitude_y)
    {
        $i = $j = $c = $point = 0;
        for ($i = 0, $j = $points_polygon; $i < $points_polygon; $j = $i++) {
            $point = $i;
            if ($point == $points_polygon)
                $point = 0;
            if ((($vertices_y[$point] > $latitude_y != ($vertices_y[$j] > $latitude_y)) && ($longitude_x < ($vertices_x[$j] - $vertices_x[$point]) * ($latitude_y - $vertices_y[$point]) / ($vertices_y[$j] - $vertices_y[$point]) + $vertices_x[$point])))
                $c = !$c;
        }
        return $c;
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
        //
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
