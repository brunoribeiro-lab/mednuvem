<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifierList extends Model {

    use HasFactory;

    public $timestamps = false;
    protected $table = "notifier_list";
    protected $fillable = [
        'deletado',
        'ref',
        'sended',
        'send_to',
        'subject',
        'email',
        'error',
        'created_at',
        'sended_at'
    ];

}
