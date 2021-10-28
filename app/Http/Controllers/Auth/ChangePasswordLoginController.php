<?php

namespace App\Http\Controllers\Auth;

use App\Models;
use App\Http\Controllers\Controller;
use App\Http\Controllers;
use App\Http\Controllers\Helper;
use Validator;
use Illuminate\Http\Request;

class ChangePasswordLoginController extends Controller
{
    public function __construct(Request $request){
        $this->middleware('auth');
    }

    public function changePassword(Request $request){
        $validation = new Helper\ValidationController('changePassUserLogin');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);

        if($validator->fails()){
            $result = "Operasi Gagal";

            return response()->json(['message'=>'Failed','info'=>$result]);#,'input'=>$return_data
        }

        $user = Models\User::where('uuid',$request->user()->uuid)->get();
        if(count($user)==0){
            return response()->json(['message'=>'Failed','info'
            => 'Token Tidak Terdaftar']);
        }
        if(!password_verify($request->password_old,$request->user()->password)){
            return response()->json([
                'message'=>'Failed',
                'info'=> 'Password Lama Tidak Sesuai']);
        }

       
        $data = [
            'password'  => bcrypt(request('password')),
            'uuid'      => $request->user()->uuid
        ];

        $input = new Helper\UpdateController('changePassUserLogin',$data); 
        
        return response()->json(['message'=>'Success','info'
        => 'Password Berhasil Diperbarui']);
    }
}
