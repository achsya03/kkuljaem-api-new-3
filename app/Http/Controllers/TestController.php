<?php

namespace App\Http\Controllers;

use Storage;
use App\Dropbox;
use Illuminate\Http\Request;

use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client;
use Spatie\FlysystemDropbox\DropboxAdapter;
class TestController extends Controller
{
    public function test(Request $request){

        dd(phpinfo());
    }
}
