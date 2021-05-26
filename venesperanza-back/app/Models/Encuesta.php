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
        'primer_apellido', 'segundo_apellido', 'sexo', 'otrosexo', 'fecha_nacimiento', 'nacionalidad','cual_otro_nacionalidad','tipo_documento',
        'cual_otro_tipo_documento', 'numero_documento', 'compartir_foto_documento_encuestado','url_foto_documento_encuestado', 'id_departamento', 'municipio_registro', 'barrio', 'direccion',
        'numero_contacto', 'linea_contacto_propia', 'preguntar_en_caso_de_llamar', 'linea_asociada_whatsapp','numero_whatsapp_principal',
        'numero_alternativo', 'linea_contacto_alternativo', 'linea_alternativa_asociada_whatsapp',
        'tiene_cuenta_facebook','cuenta_facebook',
        'correo_electronico', 'comentario', 'unico_miembro_hogar', 'mujeres_embarazadas', 'mujeres_lactantes', 'situacion_discapacidad',
        'enfermedades_cronicas', 'falta_comida', 'cuantas_veces_falta_comida', 'dormir_sin_comer', 'cuantas_veces_dormir_sin_comer',
        'todo_dia_sin_comer', 'cuantas_veces_todo_dia_sin_comer', 'satisfaccion_necesidades_basicas', 'tipo_vivienda_alojamiento_15_dias',
        'ingresos_c', 'total_gastos', 'gastos_percapita1', 'gasto_hogar', 'codigo_encuesta', 'puntaje_paso_tres',
        'puntaje_paso_cuatro', 'puntaje_paso_cinco', 'puntaje_paso_seis', 'puntaje_paso_siete', 'puntaje_paso_ocho',
        'fecha_llegada_pais', 'estar_dentro_colombia', 'pais_destino_final','como_llego_al_formulario',
        '  id_municipio_destino_final','id_departamento_destino_final',
        'total_miembros_hogar', 'donde_encontro_formulario','podemos_contactarte','forma_contactarte','otra_forma_contactarte',
        'ubicacion', 'numero_entregado_venesperanza','razon_elegir_destino_final','otra_razon_elegir_destino_final','recibe_transporte_humanitario', 'id_kobo'
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
