<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;


class VerifyEmailController extends Controller
{
    
    public function __invoke(Request $request)
    {
        $validation = new Helper\ValidationController('verifyUser');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        if($validator->fails()){
            $result = "Operasi Gagal";
            // return response()->json([
			// 	'message' => 'Failed',
			// 	'info' => 'Operasi Gagal',
			// 	//'data' => $result
			// ]);
            return Redirect::to(env('APP_URL', "https://kkuljaem.xyz").'register-3')->with( ['status'=>'error'] );
        }

        $user = User::where('web_token',$request->token)->get();
        if(count($user)==0){
            // return response()->json([
			// 	'message' => 'Failed',
			// 	'info' => 'Token Tidak Ssesuai',
			// 	//'data' => $result
			// ]);
            return Redirect::to(env('APP_URL', "https://kkuljaem.xyz").'register-3')->with( ['status'=>'error'] );
        }
        
        $old_web_token = $request->token;
        $web_token = $validation->data['web_token'];

        $data = [
            'old_web_token'  => $old_web_token,
            'password'       => bcrypt(request('password')),
            'web_token'      => $web_token
        ];

        $input = new Helper\UpdateController('verifyUser',$data);
        
        return Redirect::to(env('APP_URL', "https://kkuljaem.xyz").'register-3')->with( ['status'=>'email-validate'] );
    }

    public function force(Request $request)
    {
        $validation = new Helper\ValidationController('verifyUser');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        // $validator = Validator::make($request->all(), $this->rules, $this->messages);
        // #echo $web_token;
        // if($validator->fails()){
        //     $result = "Operasi Gagal";

        //     return response()->json(['message'=>'Failed','info'=>$result]);#,'input'=>$return_data
        // }

        $admin = User::where('email',$request->admin_email)->where('password',bcrypt($request->admin_password))->where('jenis_pengguna','2')->get();
        if(count($admin)==0){
            return response()->json([
				'message' => 'Failed',
				'info' => 'Admin Tidak Terdaftar',
				//'data' => $result
			]);
            // return Redirect::to(env('APP_URL', "https://kkuljaem.xyz").'register-3')->with( ['status'=>'error'] );
        }

        $user = User::where('email',$request->email)->get();
        if(count($user)==0){
            return response()->json([
				'message' => 'Failed',
				'info' => 'Email Tidak Terdaftar',
				//'data' => $result
			]);
            // return Redirect::to(env('APP_URL', "https://kkuljaem.xyz").'register-3')->with( ['status'=>'error'] );
        }
        
        $old_web_token = $user->web_token;
        $web_token = $validation->data['web_token'];

        $data = [
            'old_web_token'  => $old_web_token,
            'password'       => bcrypt(request('password')),
            'web_token'      => $web_token
        ];

        $input = new Helper\UpdateController('verifyUser',$data);
        
        return response()->json([
            'message' => 'Success',
            'info' => 'Verifikasi Berhasil',
            //'data' => $result
        ]);
        // return Redirect::to(env('APP_URL', "https://kkuljaem.xyz").'register-3')->with( ['status'=>'email-validate'] );
    }
}
