<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiembrosHogar extends Model
{
    use HasFactory;

    protected $table = 'miembros_hogar';

    protected $fillable = ['id_encuesta','primer_nombre_miembro', 'segundo_nombre_miembro',
    'primer_apellido_miembro','segundo_apellido_miembro','sexo_miembro','fecha_nacimiento', 'codigo_encuesta',
    'nacionalidad', 'cual_otro_nacionalidad', 'tipo_documento', 'cual_otro_tipo_documento','numero_documento',
    'compartir_foto_documento', 'url_foto_documento' ];

    public function encuesta()
    {
        return $this->belongsTo('App\Models\Encuesta','id_encuesta');
        
    }
}
