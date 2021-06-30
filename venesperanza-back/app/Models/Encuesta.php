<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Encuesta;

class Encuesta extends Model
{
    use HasFactory;

    protected $table = 'encuesta';


    protected $fillable = ['fuente','consentimiento_compartido', 'primer_nombre', 'segundo_nombre',
        'primer_apellido', 'segundo_apellido','tipo_documento',
        'cual_otro_tipo_documento', 'numero_documento',
        'numero_contacto', 'linea_contacto_propia', 'linea_asociada_whatsapp',
        'correo_electronico', 'codigo_encuesta', 
        'fecha_llegada_pais', 'como_llego_al_formulario','numero_entregado_venesperanza', 'id_kobo'
    ];

    protected $appends = ['puntaje'];

    public function departamento()
    {
        return $this->belongsTo('App\Models\Departamento', 'id_departamento');
    }

    public function municipio()
    {
        return $this->belongsTo('App\Models\Municipio', 'id_municipio');
    }

    public function departamento_destino_final()
    {
        return $this->belongsTo('App\Models\Departamento', 'id_departamento_destino_final');
    }

    public function municipio_destino_final()
    {
        return $this->belongsTo('App\Models\Municipio', 'id_municipio_destino_final');
    }

    public function necesidadesbasicas()
    {
        return $this->belongsToMany('App\Models\NecesidadBasica', 'encuesta_necesidades_basicas', 'id_encuesta');

    }

    public function miembroshogar()
    {
        return $this->hasMany('App\Models\MiembrosHogar', 'id_encuesta');
    }

    public function getPuntajeAttribute()
    {
        return $this->puntaje_paso_tres + $this->puntaje_paso_cuatro + $this->puntaje_paso_cinco
            + $this->puntaje_paso_seis + $this->puntaje_paso_siete + $this->puntaje_paso_ocho;
    }


}
