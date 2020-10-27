<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Validation extends Model
{
    use HasFactory;

    protected $table = 'validacion';

    protected $fillable = ['ip', 'fbclid','url_origen'];

}
