<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogsMensajesAuto extends Model
{
    use HasFactory;

    protected $table = 'log_mensajes';

    protected $fillable = [ 'waId', 'mensaje','tipo_mensaje'];

}
