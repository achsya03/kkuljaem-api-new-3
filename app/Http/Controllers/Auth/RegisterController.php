<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\MailController;
use Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    private $rules = [];

    private $messages = [];

    public function __invoke(Request $request)
    {
        $random = rand(0, 999999);
        $web_token = Str::random(144);

        $info_pengguna=[
            #'nama' => request('nama'),
            'email' => request('email'),
            'web_token' => $web_token,
        ];

        if(count($usr = User::where('email',request('email'))
                    ->where('email_verified_at',null)->get())>0){


            if(!$kirim_email = MailController::sendEmail($info_pengguna,"verify")){
                return response()->json(['message'=>'Failed','info'
                => 'Email Gagal Dikirim']);
            }

            $usr = User::where('email',request('email'))->update([
                'email' => request('email'),
                'password' => bcrypt(request('password')),
                'web_token' => $web_token,
            ]);

            return response()->json([
                'message'=>'Success',
                'info'=> 'Silakan Konfirmasi Email Anda'
            ]);
        }


        $validation = new Helper\ValidationController('authUser');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        
        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        if($validator->fails()){
            // $return_data=$request->all();
            // unset($return_data['password']);
            // unset($return_data['password_confirmation']);
            $result = "Operasi Gagal";

            return response()->json(['message'=>'Failed','info'=>$result]);#,'input'=>$return_data
        }

        $web_token = $validation->data['web_token'];

        $info_pengguna=[
            #'nama' => request('nama'),
            'email' => request('email'),
            'web_token' => $web_token,
        ];

        if(!$kirim_email = MailController::sendEmail($info_pengguna,"verify")){
            return response()->json(['message'=>'Failed','info'
            => 'Email Gagal Dikirim']);
        }
        if($kirim_email != 'Mail Sended'){
            return response()->json(['message'=>'Failed','info'
            => 'Email Gagal Dikirim, Kirim Lagi']);
        }
        $uuid = $validation->data['uuid'];

        $data = [
            'nama' => 'Student-'.$random,
            'email' => request('email'),
            'password' => bcrypt(request('password')),
            'web_token' => $web_token,
            'jenis_pengguna' => 0,
            'jenis_akun' => 0,
            'jenis_akun' => 0,
            'uuid' => $uuid
        ];

        $input = new Helper\InputController('authUser',$data);
        
        return response()->json(['message'=>'Success',
        'info'=> $kirim_email]);
    }
    /*public function apiRequest(Request $request){
        
        
    }*/

    /*public function webRequest(Request $request){
        $validator = Validator::make($request->all(), $this->rules, $this->messages);
  
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput($request->all);
        }

        $web_token = $this->randomToken(144);

        $info_pengguna=[
            #'nama' => request('nama'),
            'email' => request('email'),
            'web_token' => $web_token,
        ];

        if(!$kirim_email=MailController::sendEmail($info_pengguna,"verify")){
            return response("Email Not Send.", 401);
        }

        User::create([
            #'nama' => request('nama'),
            'email' => request('email'),
            'password' => bcrypt(request('password')),
            'web_token' => $web_token,
            'jenis_pengguna' => 0,
            'jenis_akun' => 0
        ]);
        
        
        return redirect()->route('login');
    }*/
}