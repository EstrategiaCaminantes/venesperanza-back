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



//rutas dashboard

//login 
Route::group(['middleware' => 'DashboardApiToken'], function(){
    Route::post('loginDashboard', 'UserController@dashboardLogin');
});

//consulta de encuestas y logout de usuario
Route::group(['middleware' => 'DashboardAuthenticate'], function(){
    Route::get('encuestasdata', 'EncuestaController@index');
    Route::get('logout/{token}', 'UserController@logout');


});



//rutas formulario de encuestas:

//validacion de usuario
Route::post('validateUser', 'MunicipioController@validarUbicacionEnVRosario');

//Route::get('asignarcodigospuntajes','EncuestaController@asignarcodigospuntajes');
Route::resource('encuestas', 'EncuestaController');

//login del formulario de encuesta
Route::group(['middleware' => 'APIToken'], function () {
    Route::post('login', 'UserController@login');
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::resource('users', 'UserController');
    Route::resource('autorizacion', 'AutorizacionController');
    Route::resource('departamentos', 'DepartamentoController');
    Route::resource('municipios', 'MunicipioController');
    Route::get('barrios', 'MunicipioController@obtenerBarrios');
    Route::resource('necesidadesbasicas', 'NecesidadesBasicasController');
});
Route::post('validation', 'ValidationController@validacionUsuario');
Route::post('matiw', 'WebhookController@webhookmati');
