<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NecesidadBasica extends Model
{
    use HasFactory;

    protected $table = 'necesidad_basica';


    protected $fillable = ['id','nombre_necesidad'];

    public function encuesta()
    {
        return $this->belongsToMany('App\Encuesta', 'encuesta_necesidades_basicas', 'id_necesidad_basica');

    }


}
