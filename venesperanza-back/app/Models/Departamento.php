<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    use HasFactory;

    protected $table = 'departamento';


    protected $fillable = ['nombre', 'coddane'];

    Public function municipios(){
    	return $this->hasMany('App\Municipio');
    }

    public function encuesta()
    {
        return $this->hasMany('App\Encuesta', 'id_departamento');
    } 


}
