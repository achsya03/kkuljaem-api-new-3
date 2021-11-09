<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Helper;
use Validator;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function __invoke(Request $request)
    {
        if(!isset($request->role) && in_array($request->role,['student','mentor','admin'])){

        }
        $validation = new Helper\ValidationController('login');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $jenis_pengguna=['Siswa','Mentor','Admin'];
        $jenis_akun=['No Sign','Helm','Crown Silver'];

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        if($validator->fails()){
            // $return_data=$request->all();
            // unset($return_data['password']);
            $result = "Operasi Gagal";

            return response()->json(['message'=>'Failed','info'=>"Operasi Gagal"]);#,'input'=>$return_data
        }

        
        $user=User::where('email',$request->user()->email)
            ->whereNotNull('email_verified_at')->first();

        if($user==null){
            return response()->json(['message'=>'Failed','info'
            => 'Silakan Verifikasi Email Terlebih Dahulu']);
        }
        if($user->jenis_pengguna == 0 && $request->role != 'student'){
            return response()->json([
                'message'=>'Failed',
                'info'=> 'Role Tidak Sesuai'
            ]);
        }if($user->jenis_pengguna == 1 && $request->role != 'mentor'){
            return response()->json([
                'message'=>'Failed',
                'info'=> 'Role Tidak Sesuai'
            ]);
        }if($user->jenis_pengguna == 2 && $request->role != 'mentor'){
            return response()->json([
                'message'=>'Failed',
                'info'=> 'Role Tidak Sesuai'
            ]);
        }
        $session=Session::where('user_id',$user->id)->delete();
        if(!$token = auth()->attempt($request->only('email','password'))){
            return response()->json([
                'message'=>'Failed',
                'info'=> 'Email Atau Password Salah'
            ]);
        }


        $data = [
            'email'           => $request->user()->email,
            'device_id'       => $request->device_id,
            'lokasi'          => $request->lokasi
        ];


        $input = new Helper\UpdateController('login',$data);
        $result = [
            'bearer_token'=>$token,
            #'nama'=>$user[0]->nama,
            'jenis_pengguna'=>$jenis_pengguna[$request->user()->jenis_pengguna],
            'jenis_akun'=>$jenis_akun[$request->user()->jenis_akun]
        ];

        return response()->json(['message'=>'Success','data'=>$result
        ]);
    }

    /*public function apiRequest(Request $request){
        
    }*/

    /*public function webRequest(Request $request){
  
        $validator = Validator::make($request->all(), $this->rules, $this->messages);
  
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput($request->all);
        }

        $web_token = RegisterController::randomToken(144);
        $device_id = $request->device_id;

        if(!$token = auth()->attempt($request->only('email','password'))){
            return response("Wrong Email or Password", 401);
        }
        $user=User::where('email',$request->user()->email)
            ->where('email_verified_at','!=','NULL')->get();
        if(count($user)==0){
            return response("Please Verify Your Email First", 401);
        }
        User::where('email','=',$request->user()->email)
            ->update(['device_id' => $device_id]);

        return response()->json(['token'=>$token,
            #'nama'=>$user[0]->nama,
            'jenis_pengguna'=>$jenis_pengguna[$user[0]->jenis_pengguna],
            'jenis_akun'=>$jenis_akun[$user[0]->jenis_akun]
        ]);
    }*/
}
