<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barrio extends Model
{
    use HasFactory;


    protected $table = 'barrios';


    protected $fillable = ['nombre', 'id_municipio','coddane_municipio'];

    public function municipio()
    {
        return $this->belongsTo('App\Municipio', 'id_municipio');
    }

    

}