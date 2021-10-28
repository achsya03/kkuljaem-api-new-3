<?php

namespace App\Http\Controllers\Banner;

use App\Models\Videos;
use App\Models\Words;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\Helper;

class VideoController extends Controller
{

    public function addDataVideo(Request $request)
    {
        $validation = new Helper\ValidationController('bannerVideo');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        $jadwal = date_format(date_create($request->jadwal),"Y/m/d");
        if($jadwal < date("Y/m/d")){
            return response()->json(['message'=>'Failed','info'=>'Tanggal dimulai dari hari ini']);
        }

        $uuid = $validation->data['uuid'];

        $data = [
            'jadwal'            => $jadwal,
            'url_video'         => request('url_video'),
            'uuid'              => $uuid
        ];

        $input = new Helper\InputController('video',$data);

        return response()->json(['message'=>'Success','info'
        => 'Proses Input Berhasil']);
    }

    public function updateDataVideo(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $validation = new Helper\ValidationController('bannerVideo');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $video = Videos::where('uuid',$uuid)->first();

        if(!$video){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;

        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        $jadwal = date_format(date_create($request->jadwal),"Y/m/d");
        if($jadwal < date("Y/m/d")){
            return response()->json(['message'=>'Failed','info'=>'Tanggal dimulai dari hari ini']);
        }

        $data = [
            'jadwal'            => $jadwal,
            'url_video'         => request('url_video'),
            'uuid'              => $uuid
        ];

        $input = new Helper\UpdateController('video',$data);

        return response()->json(['message'=>'Success','info'
        => 'Proses Update Berhasil']);

    }

    public function allDataVideoByDate(Request $request){
        if(!$jadwal=$request->jadwal){
            return response()->json(['message' => 'Failed',
            'info'=>"Jadwal Tidak Sesuai"]);
        }
        $jadwal = date_format(date_create($request->jadwal),"Y/m/d");
        if(count($video = Videos::where('jadwal',$jadwal)->get())==0){
            return response()->json(['message' => 'Success',
            'info'=>"Data Tidak Ditemukan"]);
        }
        foreach ($video as $vid) {
            unset($vid['id']);  
        }     

        $result = [
            'tanggal' => $jadwal,
            'video' => $video
        ];

        return response()->json([
            'message'=>'Success',
            'data'=> $result
        ]);
    }

    public function getContentSchedule(Request $request){
        $video = Videos::orderBy('jadwal','ASC')->get();

        foreach ($video as $vid) {
            unset($vid['id']);  
        }   
        
        return response()->json([
            'message'=>'Success',
            'data'=> $video
        ]);
    }

    public static function allDataByDate(Request $request){
        $jadwal = date_format(date_create($request->jadwal),"Y/m/d");
        if(!$jadwal or $request->jadwal == null){
            return response()->json(['message'=>'Failed','info'=>"Tanggal Tidak Sesuai"]);
        }

        $video = Videos::where('jadwal',$jadwal)
            ->first(); 
        // foreach ($video as $vid) {
        //     unset($vid['id']);  
        // }     

        $word = Words::where('jadwal',$jadwal)
            ->get(); 
        foreach ($word as $wo) {
            unset($wo['id']);  
            unset($wo['pengucapan_id']);  
        }   

        return response()->json(['message'=>'Success','data'=>['video'=>$video,'word'=>$word]]);
    }

    public static function detailDataVideo($token,$user){
        if(!$uuid=$token){
            return response()->json(['message' => 'Failed',
            'info'=>"Token Tidak Sesuai"]);
        }
        if(count(Videos::where('uuid',$uuid)->get())==0){
            return response()->json(['message' => 'Failed',
            'info'=>"Token Tidak Sesuai"]);
        }

        $video = Videos::where('uuid',$uuid)->first();
        unset($video['id']);

        return response()->json([
            'message'=>'Success',
            'account' => $user,
            'data' => $video
        ]);
    }

    public static function detailDataVideos(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message' => 'Failed',
            'info'=>"Token Tidak Sesuai"]);
        }
        if(count(Videos::where('uuid',$uuid)->get())==0){
            return response()->json(['message' => 'Failed',
            'info'=>"Token Tidak Sesuai"]);
        }

        $video = Videos::where('uuid',$uuid)->first();
        unset($video['id']);

        return response()->json([
            'message'=>'Success',
            'data' => $video
        ]);
    }

    public static function deleteData(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message' => 'Failed',
            'info'=>"Token Tidak Sesuai"]);
        }
        if(count(Videos::where('uuid',$uuid)->get())==0){
            return response()->json(['message' => 'Failed',
            'info'=>"Token Tidak Sesuai"]);
        }

        $word = Videos::where('uuid',$uuid)->delete();

        return response()->json([
            'message'=>'Success',
            'info'  => 'Proses Delete Video Hari Ini Berhasil']);
    }
}
