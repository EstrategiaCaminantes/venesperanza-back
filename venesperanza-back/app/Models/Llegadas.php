<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Llegadas extends Model
{
    use HasFactory;

    protected $table = 'llegadas';

    protected $fillable = ['tipo_documento', 'numero_documento', 'telefono', 'id_departamento', 
    'id_municipio','id_encuesta','latitud','longitud'];

    public function encuesta()
    {
        return $this->belongsTo('App\Models\Encuesta','id_encuesta');
        
    }

    public function departamento()
    {
        return $this->belongsTo('App\Models\Departamento', 'id_departamento');
    }

    public function municipio()
    {
        return $this->belongsTo('App\Models\Municipio', 'id_municipio');
    }

   
}
