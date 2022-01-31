<?php

namespace App\Http\Controllers\Post;

use App\Models;
use App\Http\Controllers\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class ThemeAdminController extends Controller
{
    private $rules = [
        'judul'              => 'required',
    ];

    private $messages = [
        'judul.required'           => 'Judul wajib diisi',
    ];

    public function addData(Request $request)
    {
        $validation = new Helper\ValidationController('theme');
        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        $uuid = $validation->data['uuid'];

        if(!$request->theme_image){
            return response()->json(['message'=>'Failed','info'=>"Image harus diisi"]);

        }

        if($request->theme_image){
            $gambar1 = $request->theme_image;
            $uploadedFileUrl1 = $validation->UUidCheck($gambar1,'Theme');

            $data = [
                'judul'            => $request->judul,
                'url_image'        => $uploadedFileUrl1['getSecurePath'],
                'id_image'         => $uploadedFileUrl1['getPublicId'],
                'jml_post'         => 0,
                'jml_like'         => 0,
                'jml_comment'      => 0,
                'uuid'              => $uuid
            ];

            $input = new Helper\InputController('theme',$data);
        }

        return response()->json(['message'=>'Success','info'
        => 'Proses Input Berhasil']);
    }

    public function updateData(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $validation = new Helper\ValidationController('theme');

        $theme = Models\Theme::where('uuid',$uuid)->first();

        if(!$theme){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;

        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        $gambar1 = $request->theme_image;
        if($gambar1){
            $uploadedFileUrl1 = $validation->UUidCheck($gambar1,'Theme');

            $data = [
                'judul'            => $request->judul,
                'url_gambar'        => $uploadedFileUrl1['getSecurePath'],
                'gambar_id'         => $uploadedFileUrl1['getPublicId'],
                'jml_post'         => $theme->jml_post,
                'jml_like'         => $theme->jml_like,
                'jml_comment'      => $theme->jml_comment,
                'uuid'              => $uuid
            ];
    
            $input = new Helper\UpdateController('theme',$data);
    
            return response()->json(['message'=>'Success','info'
            => 'Proses Update Berhasil']);
        }

        $data = [
            'judul'            => $request->judul,
            'jml_post'         => $theme->jml_post,
            'jml_like'         => $theme->jml_like,
            'jml_comment'      => $theme->jml_comment,
            'uuid'              => $uuid
        ];

        $input = new Helper\UpdateController('theme',$data);

        return response()->json(['message'=>'Success','info'
        => 'Proses Update Berhasil']);

    }

    public function allData(Request $request){

        $video_uuid = Models\Video::select('uuid')->get();
        $theme = Models\Theme::orderBy('urutan','ASC')
                ->whereNotIn('judul',$video_uuid)->get();
        foreach ($theme as $vid) {
            unset($vid['id']);  
            unset($vid['gambar_id']);  
        }     

        return response()->json(['message'=>'Success','data'
        => $theme]);
    }

    public static function detailData(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message' => 'Failed',
            'info'=>"Token Tidak Sesuai"]);
        }
        $video_uuid = Models\Video::select('uuid')->get();
        if(count($theme = Models\Theme::orderBy('jml_post','DESC')
                ->whereNotIn('judul',$video_uuid)->get())==0){
            return response()->json(['message' => 'Failed',
            'info'=>"Token Tidak Sesuai"]);
        }

        $theme = Models\Theme::where('uuid',$uuid)->first();
        unset($theme['id']);

        return response()->json([
            'message'=>'Success',
            'data' => $theme
        ]);
    }

    // public static function deleteData(Request $request){
    //     if(!$uuid=$request->token){
    //         return response()->json(['message' => 'Failed',
    //         'info'=>"Token Tidak Sesuai"]);
    //     }
    //     if(count(Videos::where('uuid',$uuid)->get())==0){
    //         return response()->json(['message' => 'Failed',
    //         'info'=>"Token Tidak Sesuai"]);
    //     }

    //     $word = Videos::where('uuid',$uuid)->delete();

    //     return response()->json([
    //         'message'=>'Success',
    //         'info'  => 'Proses Delete Video Hari Ini Berhasil']);
    // }
}
