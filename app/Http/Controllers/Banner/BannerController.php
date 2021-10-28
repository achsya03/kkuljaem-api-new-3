<?php

namespace App\Http\Controllers\Banner;

use App\Models\Banner;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Helper;
use Validator;

class BannerController extends Controller
{
    
    public function addData(Request $request)
    {
        $validation = new Helper\ValidationController('banner');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        $gambar1 = $request->url_web;
        $uploadedFileUrl1 = $validation->UUidCheck($gambar1,'Banner/Web');

        $gambar2 = $request->url_mobile;
        $uploadedFileUrl2 = $validation->UUidCheck($gambar2,'Banner/Mobile');

        $uuid = $validation->data['uuid'];

        $data = [
            'judul_banner'          => request('judul_banner'),
            'url_web'               => $uploadedFileUrl1['getSecurePath'],
            'web_id'                => $uploadedFileUrl1['getPublicId'],
            'url_mobile'            => $uploadedFileUrl2['getSecurePath'],
            'mobile_id'             => $uploadedFileUrl2['getPublicId'],
            'deskripsi'             => request('deskripsi'),
            'label'                 => request('label'),
            'link'                  => request('link'),
            'uuid'                  => $uuid
        ];

        $input = new Helper\InputController('banner',$data);

        return response()->json(['message'=>'Success','info'
        => 'Proses Input Berhasil']);
    }

    

    public function updateData(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $validation = new Helper\ValidationController('banner');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $banner = Banner::where('uuid',$uuid)->first();

        if(!$banner){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        $uploadedFileUrl1 = [
            'getSecurePath' => '',
            'getPublicId'   => ''
        ];
        $uploadedFileUrl2 = $uploadedFileUrl1;

        if(isset($request->url_web)){
            $uploadedFileUrl1 = $validation->UUidCheck($request->url_web,'Banner/Web');
            if(isset($banner->web_id) && $banner->web_id != ''){
                $validation->deleteImage($banner->web_id);
            }
        }

        if(isset($request->url_mobile)){
            $uploadedFileUrl2 = $validation->UUidCheck($request->url_mobile,'Banner/Mobile');
            if(isset($banner->mobile_id) && $banner->mobile_id != ''){
                $validation->deleteImage($banner->mobile_id);
            }
        }

        $data = [
            'judul_banner'          => request('judul_banner'),
            'url_web'               => $uploadedFileUrl1['getSecurePath'],
            'web_id'                => $uploadedFileUrl1['getPublicId'],
            'url_mobile'            => $uploadedFileUrl2['getSecurePath'],
            'mobile_id'             => $uploadedFileUrl2['getPublicId'],
            'deskripsi'             => request('deskripsi'),
            'label'                 => request('label'),
            'link'                  => request('link'),
            'uuid'                  => $uuid
        ];

        $input = new Helper\UpdateController('banner',$data);

        return response()->json(['message'=>'Success','info'
        => 'Proses Update Berhasil']);
    }

    public function allData(Request $request){

        $banner = Banner::all();
        foreach ($banner as $bann) {
            unset($bann['id']);
            unset($bann['gambar_web_id']);
            unset($bann['gambar_mobile_id']);
        }
        

        return response()->json(['message'=>'Success','data'
        => $banner]);
    }

    public static function detailData($token,$user){
        if(!$uuid=$token){
            return response()->json(['message' => 'Failed',
            'info'=>"Token Tidak Sesuai"]);
        }
        if(count(Banner::where('uuid',$uuid)->get())==0){
            return response()->json(['message' => 'Failed',
            'info'=>"Token Tidak Sesuai"]);
        }

        $banner = Banner::where('uuid',$uuid)->first();
        unset($banner['id']);
        unset($banner['gambar_web_id']);
        unset($banner['gambar_mobile_id']);

        return response()->json([
            'message'=>'Success',
            'account' => $user,
            'data'  => $banner]);
    }

    public static function detailDatas(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message' => 'Failed',
            'info'=>"Token Tidak Sesuai"]);
        }
        if(count(Banner::where('uuid',$uuid)->get())==0){
            return response()->json(['message' => 'Failed',
            'info'=>"Token Tidak Sesuai"]);
        }

        $banner = Banner::where('uuid',$uuid)->first();
        unset($banner['id']);
        unset($banner['gambar_web_id']);
        unset($banner['gambar_mobile_id']);

        return response()->json([
            'message'=>'Success',
            'data'  => $banner]);
    }

    public static function deleteData(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message' => 'Failed',
            'info'=>"Token Tidak Sesuai"]);
        }
        if(count(Banner::where('uuid',$uuid)->get())==0){
            return response()->json(['message' => 'Failed',
            'info'=>"Token Tidak Sesuai"]);
        }

        $banner = Banner::where('uuid',$uuid)->delete();

        return response()->json([
            'message'=>'Success',
            'info'  => 'Proses Delete Banner Berhasil']);
    }
}
