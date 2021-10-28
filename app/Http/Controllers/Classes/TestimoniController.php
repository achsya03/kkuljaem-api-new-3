<?php

namespace App\Http\Controllers\Classes;

use App\Models\Testimoni;
use App\Models\User;
use App\Models\Classes;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Helper;
use Validator;

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
        
        $id_user = User::where('uuid',$request->id_user)
            ->where('jenis_pengguna',0)
            ->first();
        if($id_user==null){
            return response()->json(['message'=>'Failed','info'=>'User ID Tidak Sesuai']);
        }

        $id_class = Classes::where('uuid',$request->id_class)->first();
        if($id_class==null){
            return response()->json(['message'=>'Failed','info'=>'Class ID Tidak Sesuai']);
        }

        $uuid = $validation->data['uuid'];
       
        $data = [
            'id_class'      => $id_class->id,
            'id_user'       => $id_user->id,
            'tgl_testimoni' => date("Y/m/d"),
            'testimoni'     => $request->testimoni,
            'uuid'          => $uuid
        ];

        $input = new Helper\InputController('testimoni',$data);

        return response()->json(['message'=>'Success','info'
        => 'Proses Input Berhasil']);
    }

    public function updateData(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $object = Testimoni::where('uuid',$uuid)->first();

        if(!$object){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $validation = new Helper\ValidationController('testimoni');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;

        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

                
        $id_user = User::where('uuid',$request->id_user)
            ->where('jenis_pengguna',0)
            ->first();
        if($id_user==null){
            return response()->json(['message'=>'Failed','info'=>'User ID Tidak Sesuai']);
        }

        $id_class = Classes::where('uuid',$request->id_class)->first();
        if($id_class==null){
            return response()->json(['message'=>'Failed','info'=>'Class ID Tidak Sesuai']);
        }
        
        $data = [
            'id_class'      => $id_class->id,
            'id_user'       => $id_user->id,
            'tgl_testimoni' => date("Y/m/d"),
            'testimoni'     => $request->testimoni,
            'uuid'          => $uuid
        ];

        $input = new Helper\UpdateController('testimoni',$data);

        return response()->json(['message'=>'Success','info'
        => 'Data Uploaded']);
    }
}
