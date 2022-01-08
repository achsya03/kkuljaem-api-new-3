<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Helper;
use App\Http\Controllers\MailController;
use Validator;
use Illuminate\Support\Facades\Auth;

class ForgotPasswordController extends Controller
{

    public function __invoke(Request $request)
    {
        $validation = new Helper\ValidationController('forgetPassUser');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        if($validator->fails()){
            $result = "Operasi Gagal";

            return response()->json(['message'=>'Failed','info'=>$result]);#,'input'=>$return_data
        }

        $user = User::where('email',$request->email)->first();
        if($user==null){
            return response()->json(['message'=>'Failed','info'
            => 'Email Tidak Terdaftar']);
        }
        $user_not_valid = User::where('email',$request->email)->where('email_verified_at',null)->get();
        if(count($user_not_valid)>0){
            return response()->json(['message'=>'Failed','info'=>"Silakan Verifikasi Email Terlebih Dahulu"]);
        }
        $info_pengguna=[
            'email' => $user->email,
            'web_token' => $user->web_token,
        ];

        if(!$kirim_email=MailController::sendEmail($info_pengguna,"forgot-pass")){
            return response()->json(['message'=>'Failed','info'=>"Email Gagal Dikirim"]);
        }

        if($kirim_email != 'Mail Sended'){
            return response()->json(['message'=>'Failed','info'
            => 'Email Gagal Dikirim, Kirim Lagi']);
        }

        #$this->test();

        return response()->json(['message'=>'Success','info'
        => 'Email Berhasil Dikirim']);
    }

    /*public function apiRequest(Request $request){

        
    }*/

    /*public function webRequest(Request $request){
        $rules = [
            'email'                 => 'required|email',
        ];
  
        $messages = [
            'email.required'        => 'Email wajib diisi',
            'email.email'           => 'Email tidak valid'
        ];
  
        $validator = Validator::make($request->all(), $rules, $messages);
  
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput($request->all);
        }
    }*/
}
