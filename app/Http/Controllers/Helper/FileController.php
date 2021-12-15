<?php

namespace App\Http\Controllers\Helper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    
    public function saveFile(Request $request){
        $gambar = $request->gambar;
        $newPath = $request->newPath;
        if(!$gambar){
            return response()->json(['message'=>"Only One Image Every Data"],401);
        }
        $extension = $gambar->extension();
        

        if(!$path = Storage::disk('do_spaces')->putFile($newPath,$request->file('gambar'),time().'.'.$extension,'public')){
            return response()->json(['message'=>'Image Upload Failed']);
        }

        $uploadResponse = [
            'getSecurePath'   =>  'https://kkuljaem-space.sfo3.digitaloceanspaces.com/'.$path,
            'getPublicId'     =>  $path
        ];

        return $uploadResponse;
    }

    public function showFile(Request $request){
        $file = Storage::disk('do_spaces')->get($request->path);

        $header = [
            'Content-Type' => $file->getMimeType()
        ];

        return response($file,200,$header);
    }
}
