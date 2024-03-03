<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacao extends Model {

    use HasFactory;

    protected $fillable = [
        'lida',
        'type',
        'form_contato_id',
        'form_exame_id',
        'cadastrado',
    ];
    protected $table = 'notificacoes';
    public $timestamps = false;

}
