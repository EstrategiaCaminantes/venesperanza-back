<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Validation;



class ValidationController extends Controller
{
    public function validacionTrafic(Request $request){

        //$ validation = new Validation;
        

        try {

            if(strstr($request['datos']['url_origen'],'l.facebook.com') || strstr($request['datos']['url_origen'],'facebook.com') 
            || strstr($request['datos']['url_origen'],'m.facebook.com') || strstr($request['datos']['url_origen'],'lm.facebook.com')){
                
                $ipusuario = $_SERVER['REMOTE_ADDR'];
                $validacion = new Validation;
                $validacion->fbclid = $request['datos']['facebookclid'];

                $validacion->url_origen = $request['datos']['url_origen'];
                $validacion->ip = $request['datos']['ip'];

                if($validacion->save()){

                    return ['exito'=>$validacion,'ip:'=>$ipusuario];
                } else{
                    return "error";
                }
            }else{
                return "error";
            }
            
            
            
        } catch (Exception $e) {
            return $e->error;
        }

        
    }
}
