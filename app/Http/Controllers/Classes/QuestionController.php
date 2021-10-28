<?php

namespace App\Http\Controllers\Classes;

use App\Models\Question;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Helper;
use Validator;

class QuestionController extends Controller
{
    public function addData(Request $request)
    {
        $counter = 0;
        
        $validation = new Helper\ValidationController('question');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        if(isset($request->url_gambar)){
            $this->rules += [
                'url_gambar'                 => 'image'
            ];
            $this->messages += [
                'url_gambar.image'           => 'Ekstensi file yang didukung jpeg dan png'
            ];
            $counter += 1;
        }
        if(isset($request->url_file)){
            $this->rules += [
                'url_file'                 => 'mimes:application/octet-stream,audio/mpeg,mpga,mp3,wav'
            ];
            $this->messages += [
                'url_file.mimes'           => 'Ekstensi file yang didukung mpeg,mpga,mp3,wav'
            ];
            $counter += 1;
        }
        if(isset($request->pertanyaan_teks)){
            $this->rules += [
                'pertanyaan_teks'                    => 'required'
            ];
            $this->messages += [
                'pertanyaan_teks.required'           => 'Teks Pertanyaan wajib diisi'
            ];
            $counter += 1;
        }

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        if($counter == 0){
            return response()->json(['message'=>'Failed','info'=>"Salah Satu Dari Pertanyaan Teks / Gambar / Video Harus Diisi"]);
        }

        $gambar1 = $request->url_gambar;
        $uploadedFileUrl1 = [
            'getSecurePath' => '',
            'getPublicId'   => ''
        ];
        if(isset($gambar1)){
            $uploadedFileUrl1 = $validation->UUidCheck($gambar1,'Class/Question/Image');
        }
        $gambar2 = $request->url_file;
        $uploadedFileUrl2 = [
            'getSecurePath' => '',
            'getPublicId'   => ''
        ];
        if(isset($gambar2)){
            $uploadedFileUrl2 = $validation->UUidCheck($gambar2,'Class/Question/Audio');
        }

        $uuid = $validation->data['uuid'];

        $data = [
            'pertanyaan_teks'       => $request->pertanyaan_teks,
            'url_gambar'            => $uploadedFileUrl1['getSecurePath'],
            'gambar_id'             => $uploadedFileUrl1['getPublicId'],
            'url_file'              => $uploadedFileUrl2['getSecurePath'],
            'file_id'               => $uploadedFileUrl2['getPublicId'],
            'jawaban'               => $request->jawaban,
            #'jenis_jawaban'         => $request->jenis_jawaban,
            'uuid'                  => $uuid
        ];

        $input = new Helper\InputController('question',$data);

        return response()->json(['message'=>'Success','info'
        => 'Proses Input Berhasil']);
    }

    public function updateData(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $counter = 0;
        
        $validation = new Helper\ValidationController('question');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        if(isset($request->url_gambar)){
            $this->rules += [
                'url_gambar'                 => 'image'
            ];
            $this->messages += [
                'url_gambar.image'           => 'Ekstensi file yang didukung jpeg dan png'
            ];
            $counter += 1;
        }
        if(isset($request->url_file)){
            $this->rules += [
                'url_file'                 => 'mimes:application/octet-stream,audio/mpeg,mpga,mp3,wav'
            ];
            $this->messages += [
                'url_file.mimes'           => 'Ekstensi file yang didukung mpeg,mpga,mp3,wav'
            ];
            $counter += 1;
        }
        if(isset($request->pertanyaan_teks)){
            $this->rules += [
                'pertanyaan_teks'                    => 'required'
            ];
            $this->messages += [
                'pertanyaan_teks.required'           => 'Teks Pertanyaan wajib diisi'
            ];
            $counter += 1;
        }

        $object = Question::where('uuid',$uuid)->first();

        if(!$object){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;

        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        if($counter == 0){
            return response()->json(['message'=>'Failed','info'=>"Salah Satu Dari Pertanyaan Teks / Gambar / Video Harus Diisi"]);
        }

        $gambar1 = $request->url_gambar;
        $uploadedFileUrl1 = [
            'getSecurePath' => '',
            'getPublicId'   => ''
        ];
        if(isset($gambar1)){
            $uploadedFileUrl1 = $validation->UUidCheck($gambar1,'Class/Question/Image');
            if($object->gambar_id != null){
                $validation->deleteImage($object->gambar_id);
            }
        }
        $gambar2 = $request->url_file;
        $uploadedFileUrl2 = [
            'getSecurePath' => '',
            'getPublicId'   => ''
        ];
        if(isset($gambar2)){
            $uploadedFileUrl2 = $validation->UUidCheck($gambar2,'Class/Question/Audio');
            if($object->file_id != null){
                $validation->deleteFile($object->file_id);
            }
        }
        
        $data = [
            'pertanyaan_teks'       => $request->pertanyaan_teks,
            'url_gambar'            => $uploadedFileUrl1['getSecurePath'],
            'gambar_id'             => $uploadedFileUrl1['getPublicId'],
            'url_file'              => $uploadedFileUrl2['getSecurePath'],
            'file_id'               => $uploadedFileUrl2['getPublicId'],
            'jawaban'               => $request->jawaban,
            #'jenis_jawaban'         => $request->jenis_jawaban,
            'uuid'                  => $uuid
        ];

        $input = new Helper\UpdateController('question',$data);

        return response()->json(['message'=>'Success','info'
        => 'Proses Update Berhasil']);
    }
}
