<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Providers\AuthServiceProvider;
use Illuminate\Support\Carbon;
use App\Providers\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Providers\Converter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class TiposDocumentos extends Model {

    use HasFactory;

    protected $table = 'tipo_de_documento'; 
}
