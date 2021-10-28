<?php

namespace App\Http\Controllers\Classes;

use App\Models;
use App\Models\ContentQuiz;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Helper;
use Validator;

class ContentQuizController extends Controller
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

        $object = Models\Quiz::where('uuid',$uuid)->first();

        if(!$object){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        #unset($object->id);

        $result = [
            //'nomor'         => $object->content->number,
            'judul'         => $object->judul,
            'keterangan'    => $object->keterangan,
            'uuid'          => $object->uuid,
        ];

        $exam = Models\Exam::where('id_quiz',$object->id)->get();
        $arr0 = [];
        for($i=0;$i<count($exam);$i++){
            $arr1 = [];
            $arr1 = [
                'nomor' => $exam[$i]->number,
                'pertanyaan' => $exam[$i]->question->pertanyaan_teks,
                'jawaban' => $exam[$i]->question->jawaban,
                'exam_uuid' => $exam[$i]->uuid,
            ];
            $arr0[$i] = $arr1;
        }
        #return $task;
        
        $result['jml_exam'] = count($arr0);
        $result['exam'] = $arr0;

        return response()->json(['message'=>'Success','data'
        => $result]);
    }

    public function addData(Request $request)
    {
        $validation1 = new Helper\ValidationController('content');
        $this->rules = $validation1->rules;
        $this->messages = $validation1->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        $validation = new Helper\ValidationController('contentQuiz');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $id_class = Models\Classes::where('uuid',$uuid)->first();

        if($id_class==null){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        //$id_class = Models\Classes::where('uuid',$request->id_class)->first();
        if($id_class==null){
            return response()->json(['message'=>'Failed','info'=>'ID Class Tidak Sesuai']);
        }

        $uuid1 = $validation1->data['uuid'];
       
        $data = [
            'id_class'      => $id_class->id,
            'number'        => $request->nomor,
            'type'          => 'quiz',
            'uuid'          => $uuid1
        ];

        $input = new Helper\InputController('content',$data);
        $uuid = $validation->data['uuid'];
        $id_content = Models\Content::where('uuid',$uuid1)->first();
       
        $data = [
            'id_content'      => $id_content->id,
            'judul'             => $request->judul,
            'keterangan'        => $request->keterangan,
            'jml_pertanyaan'    => 0,
            'uuid'              => $uuid
        ];
        $input = new Helper\InputController('contentQuiz',$data);

        $id_class1 = Models\Classes::where('id',$id_class->id)->first();
        $data = [
            'jml_kuis'              => $id_class1->jml_kuis+1,
            'uuid'                   => $id_class1->uuid
        ];

        $update = new Helper\UpdateController('classes',$data);

        return response()->json(['message'=>'Success','info'
        => 'Proses Input Berhasil']);
    }

    public function updateData(Request $request){
        $validation = new Helper\ValidationController('contentQuiz');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $object = Models\Quiz::where('uuid',$uuid)->first();

        if(!$object){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;

        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }
   
        $data = [
            'judul'             => $request->judul,
            'keterangan'        => $request->keterangan,
            'uuid'              => $uuid
        ];

        $input = new Helper\UpdateController('contentQuiz',$data);

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
        
        $quiz = Models\Quiz::where('uuid',$uuid)->get();
        if(count($quiz)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        $contents = Models\Content::where('id',$quiz[0]->id_content)->get();

        #delete content
        $delete = Models\Content::where('id',$quiz[0]->id_content)->delete();

        #delete content quiz
        $delete = Models\Quiz::where('uuid',$uuid)->delete();

        #update class
        $update = Models\Classes::where('id',$contents[0]->id_class)->update([
            'jml_kuis' => $contents[0]->classes->jml_kuis-1
        ]);

                
        if(count($contents)>0){
            #Update Number
            $contents = Models\Content::where('id_class',$contents[0]->id_class)
                        ->orderBy('number','ASC')->get();
            for($i=0;$i<count($contents);$i++){
                Models\Content::where('id',$contents[$i]->id)->update([
                    'number' => $i+1
                ]);
            }
        }

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'info'    => 'Proses Hapus Content Quiz Berhasil'
        ]);
    }

    public function detailData(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $object = Models\Quiz::where('uuid',$uuid)->first();

        if(!$object){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        #unset($object->id);

        $result = [
            'nomor'         => $object->content->number,
            'judul'         => $object->judul,
            'keterangan'    => $object->keterangan,
            'uuid'          => $object->uuid,
        ];

        return response()->json(['message'=>'Success','data'
        => $result]);
    }
}
