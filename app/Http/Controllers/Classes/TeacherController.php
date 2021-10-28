<?php

namespace App\Http\Controllers\Classes;

use App\Models\Classes;
use App\Models\User;
use App\Models\Teacher;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Helper;
use Validator;

class TeacherController extends Controller
{

    public function addData(Request $request)
    {
        $validation = new Helper\ValidationController('teacher');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }
        
        $id_class = Classes::where('uuid',$request->id_class)->first();
        if($id_class==null){
            return response()->json(['message'=>'Failed','info'=>'Class ID Tidak Sesuai','input'=>$return_data]);
        }

        $id_user = User::where('uuid',$request->id_user)
        ->where('jenis_pengguna',1)
        ->first();
        if($id_user==null){
            return response()->json(['message'=>'Failed','info'=>'User ID Tidak Sesuai','input'=>$return_data]);
        }

        $uuid = $validation->data['uuid'];
       
        $data = [
            'id_user'       => $id_user->id,
            'id_class'      => $id_class->id,
            'uuid'          => $uuid
        ];

        $input = new Helper\InputController('teacher',$data);

        return response()->json(['message'=>'Success','info'
        => 'Proses Input Berhasil']);
    }

    public function updateData(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $validation = new Helper\ValidationController('teacher');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $teacher = Teacher::where('uuid',$uuid)->get();

        if(count($teacher)==0){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        $return_data=$validator->validated();
        
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors(),'input'=>$return_data]);
        }
        
        $id_class = Classes::where('uuid',$request->id_class)->first();
        if($id_class==null){
            return response()->json(['message'=>'Failed','info'=>'Class ID Tidak Sesuai','input'=>$return_data]);
        }

        $id_user = User::where('uuid',$request->id_user)
            ->where('jenis_pengguna',1)
            ->first();
        if($id_user==null){
            return response()->json(['message'=>'Failed','info'=>'User ID Tidak Sesuai','input'=>$return_data]);
        }
        
        $data = [
            'id_user'       => $id_user->id,
            'id_class'      => $id_class->id,
            'uuid'          => $uuid
        ];

        $input = new Helper\UpdateController('teacher',$data);

        return response()->json(['message'=>'Success','info'
        => 'Proses Update Berhasil']);
    }

    // public function allData(Request $request){

    //     $teacher = Teacher::all();
    //     // foreach ($teacher as $bann) {
    //     //     unset($bann['id']);
    //     //     unset($bann['id_mentor']);
    //     //     unset($bann['id_class']);
    //     // }
        

    //     return response()->json(['message'
    //     => $teacher[0]->mentor->user->nama],200);
    // }

    // public function detailData(Request $request){
    //     if(!$uuid=$request->token){
    //         return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
    //     }
    //     if(count(Banner::where('uuid',$uuid)->get())==0){
    //         return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
    //     }

    //     $banner = Banner::where('uuid',$uuid)->get();
    //     unset($banner[0]['id']);
    //     unset($banner[0]['gambar_web_id']);
    //     unset($banner[0]['gambar_mobile_id']);

    //     return response()->json(['message'
    //     => $banner[0]],200);
    // }
}
