<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Llegadas extends Model
{
    use HasFactory;

    protected $table = 'llegadas';

    protected $fillable = ['tipo_documento', 'numero_documento', 'numero_contacto','numero_contacto_asociado_whatsapp', 
    'nombre_jefe_hogar', 'donde_te_encuentras','otro_donde_te_encuentras', 'id_encuesta'];

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
