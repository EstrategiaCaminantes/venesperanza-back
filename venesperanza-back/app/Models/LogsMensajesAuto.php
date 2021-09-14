<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogsMensajesAuto extends Model
{
    use HasFactory;

    protected $table = 'logs_mensajes_automatizados';

    protected $fillable = [ 'waId', 'mensaje','tipo_mensaje'];

}
