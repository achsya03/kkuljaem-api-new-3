<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebController extends Controller
{
    // public function updateDeviceID(Request $request)
    // {
    //     if(!$uuid=$request->email){
    //         return response()->json(['message'=>'Failed','info'=>"Email Tidak Sesuai"]);
    //     }

    //     if(count($user_upd = Models\User::where('email',$request->email)->get())==0){
    //         return response()->json(['message'=>'Failed','info'=>"Email Tidak Sesuai"]);
    //     }

       
    //     $user_upd = Models\User::where('email',$request->email)
    //             ->update([
    //                 'device_id' => $request->device_id,
    //             ]);

    //     return response()->json(['message'=>'Success','info'
    //     => 'Proses Update Device ID Berhasil']);
    // }

}
