<?php

namespace App\Http\Controllers\Classes;

use App\Models;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper;
use Validator;
use Illuminate\Http\Request;

class ShadowingController extends Controller
{
    public function checkData(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        if(count($video = Models\Video::where('uuid',$uuid)->get())==0){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        $shadowing = Models\Shadowing::where('id_video',$video[0]->id)->get();
        $result['nomor_shadowing'] = count($shadowing)+1;
    
        return response()->json(['message'=>'Success','data'
        => $result]);
    }

    public function addData(Request $request)
    {
        $validation = new Helper\ValidationController('shadowing');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        $return_data=$validator->validated();
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        $validation1 = new Helper\ValidationController('word');
        $this->rules = $validation1->rules;
        $this->messages = $validation1->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        $return_data=$validator->validated();
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }
        
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        if(count($video = Models\Video::where('uuid',$uuid)->get())==0){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $uuid1 = $validation1->data['uuid'];
        $gambar1 = $request->url_pengucapan;
        $uploadedFileUrl1 = $validation->UUidCheck($gambar1,'Word');
    
        // if($request->url_pertanyaan != null){
        //     $url_pertanyaan = $request->url_pertanyaan;
        // }
        $data = [
            'jadwal'                   => '2002/02/02',
            'hangeul'                  => $request->hangeul,
            'pelafalan'                => $request->pelafalan,
            'penjelasan'               => $request->penjelasan,
            'url_pengucapan'           => $uploadedFileUrl1['getSecurePath'],
            'pengucapan_id'                  => $uploadedFileUrl1['getPublicId'],
            'uuid'                     => $uuid1
        ];

        $input = new Helper\InputController('word',$data);

        $word = Models\Words::where('uuid',$uuid1)->first();
        
        $uuid1 = $validation->data['uuid'];

        $data = [
            'id_word'                   => $word->id,
            'id_video'                  => $video[0]->id,
            'number'                    => $request->nomor,
            'uuid'                      => $uuid1
        ];

        $input = new Helper\InputController('shadowing',$data);

        $data = [
            
            'jml_shadowing'             => $video[0]->jml_shadowing+1,
            'uuid'                      => $video[0]->uuid
        ];

        $update = new Helper\UpdateController('contentVideo',$data);


        return response()->json(['message'=>'Success','info'
        => 'Proses Input Berhasil']);
    }
    
    public function detailData(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        if(count($shadowing = Models\Shadowing::where('uuid',$uuid)->get())==0){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $result['nomor'] = $shadowing[0]->number;
        $result['hangeul'] = $shadowing[0]->word->hangeul;
        $result['pelafalan'] = $shadowing[0]->word->pelafalan;
        $result['penjelasan'] = $shadowing[0]->word->penjelasan;
        $result['shadowing_uuid'] = $shadowing[0]->uuid;

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'data'    => $result
        ]);
    }

    public function updateData(Request $request)
    {
        $validation = new Helper\ValidationController('shadowing');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        $return_data=$validator->validated();
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        $validation1 = new Helper\ValidationController('word');
        $this->rules = $validation1->rules;
        $this->messages = $validation1->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        $return_data=$validator->validated();
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }
                
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        if(count($shadowing = Models\Shadowing::where('uuid',$uuid)->get())==0){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $uploadedFileUrl1 = [
            'getSecurePath' => '',
            'getPublicId'   => ''
        ];

        $uuid1 = $shadowing[0]->word->uuid;
        $gambar1 = $request->url_pengucapan;
        if(isset($gambar1)){
            $uploadedFileUrl1 = $validation->UUidCheck($gambar1,'Word');
            if(isset($word->pengucapan_id) && $word->pengucapan_id != ''){
                $validation->deleteImage($word->pengucapan_id);
            }
        }

        $data = [
            'jadwal'                   => '2002/02/02',
            'hangeul'                  => $request->hangeul,
            'pelafalan'                => $request->pelafalan,
            'penjelasan'               => $request->penjelasan,
            'url_pengucapan'           => $uploadedFileUrl1['getSecurePath'],
            'pengucapan_id'            => $uploadedFileUrl1['getPublicId'],
            'uuid'                     => $uuid1
        ];

        $input = new Helper\UpdateController('word',$data);
        
        return response()->json(['message'=>'Success','info'
        => 'Proses Update Berhasil']);
    }

    public function deleteData(Request $request)
    {
        $result = [];
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        
        $shadowing = Models\Shadowing::where('uuid',$uuid)->get();
        if(count($shadowing)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        #update video
        $video = Models\Video::where('id',$shadowing[0]->id_video)->update([
            'jml_shadowing' => $shadowing[0]->video->jml_shadowing-1
        ]);


        #delete words
        $delete = Models\Words::where('id',$shadowing[0]->id_word)->delete();

        #delete task
        $delete = Models\Shadowing::where('uuid',$uuid)->delete();
        
        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'info'    => 'Proses Hapus Content Quiz Berhasil'
        ]);
    }
}
