<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intentos extends Model
{
    use HasFactory;

    protected $table = 'intentos';

    protected $fillable = ['tipo_documento', 'numero_documento', 'numero_contacto', 'correo_electronico','numero_contacto_asociado_whatsapp', 
    'nombre_jefe_hogar', 'donde_te_encuentras','otro_donde_te_encuentras',] ;
}
