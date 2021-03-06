<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    use HasFactory;

    protected $table = 'municipio';


    protected $fillable = ['nombre', 'id_departamento','coddane'];

    public function departamento()
    {
        return $this->belongsTo('App\Departamento', 'id_departamento');
    }

    Public function barrios(){
    	return $this->hasMany('App\Barrios');
    }

    Public function encuestas(){
    	return $this->hasMany('App\Encuesta');
    }

    Public function llegadas(){
    	return $this->hasMany('App\Llegadas');
    }
}
