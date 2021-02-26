<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Autorizacion extends Model
{
    use HasFactory;

    protected $table = 'autorizaciones';


    protected $fillable = [
        'tratamiento_datos', 'terminos_condiciones','condiciones','ip','id_encuesta','latitud','longitud'
    ];

   
    Public function encuesta(){
        return $this->belongsTo('App\Encuesta', 'id_encuesta');

    }
}
