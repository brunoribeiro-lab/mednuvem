<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class Activity extends Model {

    use HasFactory;

    protected $table = '_RECENT_ACTIVITY';
    protected $fillable = ['USER', 'ICON', 'TITLE', 'CREATED'];
    public $timestamps = false;

    public static function novo($title, $icon = "user-plus") {
        $activity = new Activity();

        $activity->USER = Auth::user()->user_id;
        $activity->ICON = $icon;
        $activity->TITLE = $title;
        $activity->CREATED = Carbon::now();

        $activity->save();
    }

}
