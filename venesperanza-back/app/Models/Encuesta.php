<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Encuesta;

class Encuesta extends Model
{
    use HasFactory;

    protected $table = 'encuesta';


    protected $fillable = ['consentimiento_compartido', 'primer_nombre', 'segundo_nombre',
        'primer_apellido', 'segundo_apellido', 'sexo', 'otrosexo', 'fecha_nacimiento', 'nacionalidad', 'tipo_documento',
        'cual_otro_tipo_documento', 'numero_documento', 'id_departamento', 'municipio_registro', 'barrio', 'direccion',
        'numero_contacto', 'linea_contacto_propia', 'preguntar_en_caso_de_llamar', 'linea_asociada_whatsapp',
        'correo_electronico', 'comentario', 'unico_miembro_hogar', 'mujeres_embarazadas', 'mujeres_lactantes', 'situacion_discapacidad',
        'enfermedades_cronicas', 'falta_comida', 'cuantas_veces_falta_comida', 'dormir_sin_comer', 'cuantas_veces_dormir_sin_comer',
        'todo_dia_sin_comer', 'cuantas_veces_todo_dia_sin_comer', 'satisfaccion_necesidades_basicas', 'tipo_vivienda_alojamiento_15_dias',
        'ingresos_c', 'total_gastos', 'gastos_percapita1', 'gasto_hogar'];

    public function departamento()
    {
        return $this->belongsTo('App\Models\Departamento', 'id_departamento');
    }

    public function municipio()
    {
        return $this->belongsTo('App\Models\Municipio', 'id_municipio');
    }


    public function necesidadesbasicas()
    {
        return $this->belongsToMany('App\Models\NecesidadBasica', 'encuesta_necesidades_basicas', 'id_encuesta');

    }

    public function miembroshogar()
    {
        return $this->hasMany('App\Models\MiembrosHogar');
    }


}
