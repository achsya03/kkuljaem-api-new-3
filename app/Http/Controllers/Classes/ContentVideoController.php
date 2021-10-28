<?php

namespace App\Http\Controllers\Classes;

use App\Models;
use App\Models\ContentVideo;
use App\Models\Classes;
use App\Models\ContentQuiz;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Helper;
use Validator;

class ContentVideoController extends Controller
{
    public function checkData(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        if(count($classes = Models\Classes::where('uuid',$uuid)->get())==0){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        $content = Models\Content::where('id_class',$classes[0]->id)
                ->get();
        $result['nomor_materi'] = count($content)+1;
    
        return response()->json(['message'=>'Success','data'
        => $result]);
    }

    public function getData(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $object = Models\Video::where('uuid',$uuid)->first();

        if(!$object){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        #unset($object->id);

        $result = [
            //'nomor'         => $object->content->number,
            'judul'         => $object->judul,
            'keterangan'    => $object->keterangan,
            'url_video'     => $object->url_video,
            'uuid'          => $object->uuid,
        ];

        $task = Models\Task::where('id_video',$object->id)->get();
        $arr0 = [];
        for($i=0;$i<count($task);$i++){
            $arr1 = [];
            $arr1 = [
                'nomor' => $task[$i]->number,
                'pertanyaan' => $task[$i]->question->pertanyaan_teks,
                'jawaban' => $task[$i]->question->jawaban,
                'task_uuid' => $task[$i]->uuid,
            ];
            $arr0[$i] = $arr1;
        }
        #return $task;
        $shadowing = Models\Shadowing::where('id_video',$object->id)->get();
        $arr = [];
        for($i=0;$i<count($shadowing);$i++){
            $arr1 = [];
            $arr1 = [
                'nomor' => $shadowing[$i]->number,
                'hangeul' => $shadowing[$i]->word->hangeul,
                'pelafalan' => $shadowing[$i]->word->pelafalan,
                'penjelasan' => $shadowing[$i]->word->penjelasan,
                'url_pengucapan' => $shadowing[$i]->word->url_pengucapan,
                'shadowing_uuid' => $shadowing[$i]->uuid,
            ];
            $arr[$i] = $arr1;
        }
        $result['jml_task'] = count($arr0);
        $result['jml_shadowing'] = count($arr);
        $result['task'] = $arr0;
        $result['shadowing'] = $arr;

        return response()->json(['message'=>'Success','data'
        => $result]);
    }
    public function addData(Request $request)
    {
        $validation1 = new Helper\ValidationController('content');
        $this->rules = $validation1->rules;
        $this->messages = $validation1->messages;

        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $id_class = Models\Classes::where('uuid',$uuid)->first();

        if($id_class==null){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        $validation = new Helper\ValidationController('contentVideo');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }


        //$id_class = Classes::where('uuid',$request->id_class)->first();
        if($id_class==null){
            return response()->json(['message'=>'Failed','info'=>'ID Class Tidak Sesuai']);
        }

        $uuid1 = $validation1->data['uuid'];
       
        $data = [
            'id_class'      => $id_class->id,
            'number'        => $request->nomor,
            'type'          => 'video',
            'uuid'          => $uuid1
        ];

        $input = new Helper\InputController('content',$data);

        $uuid = $validation->data['uuid'];
        $id_content = Models\Content::where('uuid',$uuid1)->first();
        $data = [
            'id_content'      => $id_content->id,
            'judul'           => $request->judul,
            'keterangan'      => $request->keterangan,
            'jml_latihan'     => 0,
            'jml_shadowing'   => 0,
            'url_video'       => $request->url_video,
            'uuid'            => $uuid
        ];

        $input = new Helper\InputController('contentVideo',$data);

        $id_class1 = Models\Classes::where('id',$id_class->id)->first();
        $data = [
            'jml_video'              => $id_class1->jml_video+1,
            'uuid'                   => $id_class1->uuid
        ];

        $update = new Helper\UpdateController('classes',$data);

        return response()->json(['message'=>'Success','info'
        => 'Proses Input Berhasil']);
    }

    public function updateData(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $validation = new Helper\ValidationController('contentVideo');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $id_video = Models\Video::where('uuid',$uuid)->first();

        if($id_video==null){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;

        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

       
        $data = [
            'judul'           => $request->judul,
            'keterangan'      => $request->keterangan,
            'url_video'       => $request->url_video,
            'uuid'            => $uuid
        ];

        $input = new Helper\UpdateController('contentVideo',$data);

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
        
        $video = Models\Video::where('uuid',$uuid)->get();
        if(count($video)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        $contents = Models\Content::where('id',$video[0]->id_content)->get();


        #delete video_theme
        if(count($video[0]->videoTheme)>0){
            $delete = Models\Theme::where('id',$video[0]->videoTheme[0]->id_theme)->delete();
        }

        #delete content
        $delete = Models\Content::where('id',$video[0]->id_content)->delete();

        #delete content quiz
        $delete = Models\Video::where('uuid',$uuid)->delete();

        #update class
        $update = Models\Classes::where('id',$contents[0]->id_class)->update([
            'jml_video' => $contents[0]->classes->jml_video-1
        ]);

        //return $contents;
        if(count($contents)>0){
            #Update Number
            $contents = Models\Content::where('id_class',$contents[0]->id_class)
                        ->orderBy('number','ASC')->get();
            //return $contents;
            for($i=0;$i<count($contents);$i++){
                Models\Content::where('id',$contents[$i]->id)->update([
                    'number' => $i+1
                ]);
            }
        }
        
        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'info'    => 'Proses Hapus Content Video Berhasil'
        ]);
    }

    public function detailData(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $object = Models\Video::where('uuid',$uuid)->first();

        if(!$object){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        #unset($object->id);

        $result = [
            'nomor'         => $object->content->number,
            'judul'         => $object->judul,
            'keterangan'    => $object->keterangan,
            'url_video'     => $object->url_video,
            'uuid'          => $object->uuid,
        ];

        return response()->json(['message'=>'Success','data'
        => $result]);
    }
}
