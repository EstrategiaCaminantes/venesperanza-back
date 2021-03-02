<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatosActualizados extends Model
{
    use HasFactory;

    protected $table = 'datos_actualizados';

    protected $fillable = ['tipo_documento', 'numero_documento', 'telefono', 'correo_electronico', 'id_encuesta'];

    public function encuesta()
    {
        return $this->belongsTo('App\Models\Encuesta','id_encuesta');
        
    }
    
    
}
