<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogoutController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        if($request->user()){
            auth()->logout();
            return response()->json([
                'message' => 'Success',
                'info'    => 'Anda Berhasil Keluar Aplikasi'
            ]);
        }else{
            return response()->json([
                'message' => 'Failed',
                'info'    => 'Silakan Masuk terlebih Dahulu'
            ]);
        }
    }
}
