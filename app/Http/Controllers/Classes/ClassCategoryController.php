<?php

namespace App\Http\Controllers\Classes;

use App\Models;
use App\Models\ClassesCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\Helper;

class ClassCategoryController extends Controller
{

    public function addData(Request $request)
    {
        $validation = new Helper\ValidationController('classCategory');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        $uuid = $validation->data['uuid'];

        $data = [
            'nama'                  => request('nama'),
            'deskripsi'             => request('deskripsi'),
            'uuid'                  => $uuid
        ];

        $input = new Helper\InputController('classCategory',$data);


        return response()->json(['message'=>'Success','info'
        => 'Proses Input Berhasil']);
    }
    
    public function __construct(Request $request){
        $this->middleware('auth');
    }

    public function updateData(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $validation = new Helper\ValidationController('classCategory');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;


        $class_cat = ClassesCategory::where('uuid',$uuid)->first();

        if(!$class_cat){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;

        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }
        
        $data = [
            'nama'                  => request('nama'),
            'deskripsi'             => request('deskripsi'),
            'uuid'                  => $uuid
        ];

        $input = new Helper\UpdateController('classCategory',$data);

        return response()->json(['message'=>'Success','info'
        => 'Proses Update Berhasil']);
    }

    public function deleteData(Request $request){
        $result = [];
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        
        $class_cat = Models\ClassesCategory::where('uuid',$uuid)->get();
        if(count($class_cat)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        if(count($class_cat[0]->classes)>0){
            $classes = Models\Classes::where('id_class_category',$class_cat[0]->id)
                        ->where('status_tersedia','1')->get();
            if(count($classes)>0){
                return response()->json([
                    'message' => 'Failed',
                    'error' => 'Kategori Memiliki Beberapa Kelas Aktif'
                ]);
            }
        }

        #delete comment
        if(count($class_cat[0]->classes)>0){
            for($i=0;$i<count($class_cat[0]->classes);$i++){
                $content = $content = Models\Content::where('id_class',$class_cat[0]->classes[$i]->id)->get();
                for($j=0;$j<count($content);$j++){
                    if($content[$j]->type=='video'){
                        $task = Models\Task::where('id_video',$content[$j]->video[0]->id)->get();
                        for($k=0;$k<count($task);$k++){
                            $question = Models\Question::where('id',$task[$k]->id_question)->delete();
                        }
                        $shadowing = Models\Shadowing::where('id_video',$content[$j]->video[0]->id)->get();
                        $theme = Models\Theme::where('judul',$content[$j]->video[0]->uuid)->delete();
                        $task = Models\Task::where('id_video',$content[$j]->video[0]->id)->delete();

                        for($k=0;$k<count($shadowing);$k++){
                            $word = Models\Words::where('id',$shadowing[$k]->id_word)->delete();
                        }

                        $shadowing = Models\Shadowing::where('id_video',$content[$j]->video[0]->id)->delete();
                    }elseif($content[$j]->type=='quiz'){
                        $quiz = Models\Exam::where('id_quiz',$content[$j]->quiz[0]->id)->get();
                        for($k=0;$k<count($quiz);$k++){
                            $question = Models\Question::where('id',$quiz[$k]->id_question)->delete();
                        }
                        $quiz = Models\Exam::where('id_quiz',$content[$j]->quiz[0]->id)->delete();
                    }
                }
                $content = Models\Content::where('id_class',$class_cat[0]->classes[$i]->id)
                            ->delete();
                
            }
        }

        $delete = Models\ClassesCategory::where('uuid',$uuid)->delete();
    

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'info'    => 'Proses Hapus Category Kelas Berhasil'
        ]);
    }

    public function allData(Request $request){

        $class_cat = ClassesCategory::orderBy('urutan','ASC')->get();
        for($i=0;$i<count($class_cat);$i++) {
            $classes = Models\Classes::where('id_class_category',$class_cat[$i]->id)->get();
            unset($class_cat[$i]['id']);
            $class_cat[$i]['jml_kelas'] = count($classes);
        }
        

        return response()->json(['message'=>'Success','data'
        => $class_cat]);
    }


    public function detailData(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        if(count(ClassesCategory::where('uuid',$uuid)->get())==0){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $class_cat = ClassesCategory::where('uuid',$uuid)->first();
        unset($class_cat['id']);

        return response()->json(['message'=>'Success','data'
        => $class_cat]);
    }
}
