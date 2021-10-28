<?php

namespace App\Http\Controllers\BadWord;

use App\Models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\Helper;

class BadWordController extends Controller
{
    public function addData(Request $request)
    {
        $validation = new Helper\ValidationController('badWord');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        $uuid = $validation->data['uuid'];

        $data = [
            'kata'                  => request('kata'),
            'uuid'                  => $uuid
        ];

        $input = new Helper\InputController('badWord',$data);


        return response()->json(['message'=>'Success','info'
        => 'Proses Input Berhasil']);
    }

    public function deleteData(Request $request){
        $result = [];
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        
        $class_cat = Models\BadWord::where('uuid',$uuid)->get();
        if(count($class_cat)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        $delete = Models\BadWord::where('uuid',$uuid)->delete();
    

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'info'    => 'Proses Hapus Kata Buruk Berhasil'
        ]);
    }

    public function allData(Request $request){

        $class_cat = Models\BadWord::orderBy('kata','ASC')->get();
        for($i=0;$i<count($class_cat);$i++) {
            unset($class_cat[$i]['id']);
        }
        

        return response()->json(['message'=>'Success','data'
        => $class_cat]);
    }


}
