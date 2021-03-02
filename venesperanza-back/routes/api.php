<?php

use Illuminate\Support\Facades\Route;

//rutas dashboard

//login
Route::group(['middleware' => 'DashboardApiToken'], function () {
    Route::post('loginDashboard', 'UserController@dashboardLogin');
    Route::post('actualizardatos','DatosActualizadosController@actualizardatos');
    Route::post('reportarllegada','LlegadasController@reportarllegada');
    
});

//consulta de encuestas y logout de usuario
Route::group(['middleware' => 'DashboardAuthenticate'], function () {
    Route::get('dash', 'EncuestaController@dashboard');
    Route::get('encuestasdata', 'EncuestaController@getEncuestas');
    Route::get('logout/{token}', 'UserController@logout');
});

//rutas formulario de encuestas:
//validacion de usuario
Route::post('validateUser', 'MunicipioController@validarUbicacionEnVRosario');

Route::get('departamentosllegada', 'DepartamentoController@index');
Route::get('municipiosllegada', 'MunicipioController@index');

//Route::get('asignarcodigospuntajes','EncuestaController@asignarcodigospuntajes');
//login del formulario de encuesta
Route::group(['middleware' => 'APIToken'], function () {
    Route::post('login', 'UserController@login');
   
});
Route::group(['middleware' => 'auth:api'], function () {
    Route::resource('users', 'UserController');
    Route::resource('autorizacion', 'AutorizacionController');
    Route::resource('departamentos', 'DepartamentoController');
    Route::resource('encuestas', 'EncuestaController');
    Route::resource('municipios', 'MunicipioController');
    Route::get('barrios', 'MunicipioController@obtenerBarrios');
    Route::resource('necesidadesbasicas', 'NecesidadesBasicasController');
});
Route::post('validation', 'ValidationController@validacionUsuario');
Route::post('matiw', 'WebhookController@webhookmati');
