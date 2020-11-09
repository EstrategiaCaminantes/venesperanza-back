<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\MunicipioController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
}); 

*/
Route::group(['middleware'=>'APIToken'],function(){
    Route::post('login','UserController@login');
});

Route::post('validarUbicacionVR','MunicipioController@validarUbicacionEnVRosario');


Route::group(['middleware' => 'auth:api'], function () {
    Route::resource('users', 'UserController');

    Route::resource('autorizacion','AutorizacionController');

    Route::resource('departamentos', 'DepartamentoController');

    Route::resource('municipios', 'MunicipioController');

    Route::get('barrios','MunicipioController@obtenerBarrios');

    

    Route::resource('encuestas', 'EncuestaController');
    Route::resource('necesidadesbasicas', 'NecesidadesBasicasController');

});
Route::post('validation','ValidationController@validacionUsuario');

