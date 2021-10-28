<?php

namespace App\Http\Controllers\Testimoni;

use App\Models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\Helper;

class TestimoniController extends Controller
{
    public function addData(Request $request)
    {
        $validation = new Helper\ValidationController('testimoni');
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
            'identitas'             => request('identitas'),
            'testimoni'             => request('testimoni'),
            'uuid'                  => $uuid
        ];

        $input = new Helper\InputController('testimoni',$data);


        return response()->json(['message'=>'Success','info'
        => 'Proses Input Berhasil']);
    }

    public function getData(Request $request){

        $class_cat = Models\Testimoni::select(['nama','identitas','testimoni','uuid'])->get();
      
        return response()->json(['message'=>'Success','data'
        => $class_cat]);
    }

    public function deleteData(Request $request){
        $result = [];
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        
        $post = Models\Testimoni::where('uuid',$uuid)->get();
        if(count($post)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        #delete comment
        $delete = Models\Testimoni::where('uuid',$uuid)->delete();
    

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'info'    => 'Proses Hapus Testimoni Berhasil'
        ]);
    }
}
