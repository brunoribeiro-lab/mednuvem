<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemAccess extends Model {

    use HasFactory;

    public $timestamps = false;
    protected $table = '_SYSTEM_ACCESS';
    protected $fillable = [
        'USER',
        'ACCOUNT',
        'MENU',
        'SUBMENU',
        'SUBSUBMENU',
        'ACCESS_FORM',
        'ACCESS_LISTING',
        'ACCESS_ADD',
        'ACCESS_PREVIEW',
        'ACCESS_UPDATE',
        'ACCESS_REMOVE',
        'ACCESS_ACCESS',
        'ACCESS_PDF',
        'ACCESS_RESEND',
        'ACCESS_HISTORIC',
        'UPDATED'
    ];

}
