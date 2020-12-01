<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiembrosHogar extends Model
{
    use HasFactory;

    protected $table = 'miembros_hogar';

    protected $fillable = ['id_encuesta','primer_nombre_miembro', 'segundo_nombre_miembro',
    'primer_apellido_miembro','segundo_apellido_miembro','sexo_miembro','fecha_nacimiento', 'codigo_encuesta'];

    public function encuesta()
    {
        return $this->belongsTo('App\Models\Encuesta');
    }
}
