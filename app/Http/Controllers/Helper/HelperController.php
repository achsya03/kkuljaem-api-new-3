<?php

namespace App\Http\Controllers\Helper;

use App\Models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HelperController extends Controller
{
    public static function randomToken(){
        $web_token = Str::random(144);

        
        return $web_token;
    }
}
