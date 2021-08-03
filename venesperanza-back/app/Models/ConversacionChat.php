<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversacionChat extends Model
{
    use HasFactory;

    protected $table = 'conversacion_chatbot';

    protected $fillable = ['waId', 'profileName', 'conversation_start', 'autorizacion', 'tipo_formulario'];

}