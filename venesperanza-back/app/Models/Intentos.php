<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intentos extends Model
{
    use HasFactory;

    protected $table = 'intentos';

    protected $fillable = ['tipo_documento', 'numero_documento', 'telefono', 'correo_electronico','id_departamento', 
    'id_municipio','latitud','longitud'] ;
}
