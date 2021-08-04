<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificacionLlegada extends Model
{
    use HasFactory;

    protected $table = 'notificacion_reporte_llegada';

    protected $fillable = ['id_encuesta', 'waId', 'respuesta','reenviar','tipo_documento','numero_documento'];

    public function encuesta()
    {
        return $this->belongsTo('App\Models\Encuesta','id_encuesta');
        
    }

}
