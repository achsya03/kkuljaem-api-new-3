<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function statUser(Request $request){
        $user  = $request->user();
        //$data['email'] = $user->email;
        if($user->jenis_pengguna!='0'){
            if($user->url_foto!=null || $user->url_foto!=''){$data['foto'] = $user->url_foto;}
        }
        $data['nama'] = $user->nama;
        $data['jenis_akun'] = $jenis_akun[$user->jenis_akun];

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'data'    => $data
        ]);
    }
}
