<?php

namespace App\Http\Controllers\Helper;

use App\Models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use General;

class ForceController extends Controller
{
    public function __construct(Request $request){
        $this->middleware('auth');
    }

    public function forceSubs(Request $request){
        $result = [];
        if($request->user()->jenis_pengguna != 2){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Anda bukan admin'
            ]);
        }
        if(!$token = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        if($token != date("Y__m__d")){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Format token tidak sesuai'
            ]);
        }
        if(!$email = $request->email){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Email tidak sesuai'
            ]);
        }  
        if(count($user = Models\User::where('email',$email)->get())==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Email tidak sesuai'
            ]);
        }
        $tgl_akhir = date_format(date_create($request->tgl_akhir),"Y/m/d");
        $tgl_akh = (new \DateTime(date('Y-m-d')))->modify('+'.(30*1).' day')->format('Y-m-d');
            
        $user = Models\User::where('email',$email)->update([
            'tgl_langganan_akhir' => $tgl_akhir
        ]);

        $result = [
            'email' => $email,
            'tgl_langganan_akhir' => $tgl_akhir
        ];

        return response()->json([
			'message' => 'Success',
			'info' => 'Proses Update Berhasil',
			'data' => $result
		]);
    }
}
