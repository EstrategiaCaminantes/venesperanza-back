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

    Public function encuestas(){
    	return $this->hasMany('App\Encuesta');
    }
}
