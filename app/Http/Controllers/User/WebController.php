<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebController extends Controller
{
    public function updateDeviceID(Request $request)
    {
        if(!$uuid=$request->device_id){
            return response()->json(['message'=>'Failed','info'=>"Device ID Tidak Sesuai"]);
        }

       
        $user_upd = Models\User::where('uuid',$request->user()->uuid)
                ->update([
                    'device_id' => $request->device_id,
                ]);

        return response()->json(['message'=>'Success','info'
        => 'Proses Update Device ID Berhasil']);
    }

}
