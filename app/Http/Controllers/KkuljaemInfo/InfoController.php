<?php

namespace App\Http\Controllers\KkuljaemInfo;

use App\Models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InfoController extends Controller
{
    public function updateData(Request $request){
        $keys = ['tnc','policy','version','about'];
        if(!$key=$request->key || !in_array($request->key,$keys)){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        if($request->key == "tnc"){
            $kkuljaem_info = Models\KkuljaemInfo::where('key','tnc')->update([
                'value' => $request->value
            ]);
        }elseif($request->key == "policy"){
            $kkuljaem_info = Models\KkuljaemInfo::where('key','policy')->update([
                'value' => $request->value
            ]);
        }elseif($request->key == "version"){
            $kkuljaem_info = Models\KkuljaemInfo::where('key','ios_ver')->update([
                'value' => $request->ios_ver,
            ]);
            $kkuljaem_info = Models\KkuljaemInfo::where('key','and_ver')->update([
                'value' => $request->and_ver,
            ]);
        }elseif($request->key == "about"){
            $kkuljaem_info = Models\KkuljaemInfo::where('key','about')->update([
                'value' => $request->value
            ]);
        }

        return response()->json(['message'=>'Success','info'
        => 'Proses Update Berhasil']);
    }

    public function getData(Request $request){
        $keys = ['tnc','policy','version','about'];
        if(!$key=$request->key || !in_array($request->key,$keys)){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $kkuljaem_info = [];

        if($request->key == "tnc"){
            $kkuljaem_info = Models\KkuljaemInfo::select(['key','value'])->where('key','tnc')->first();
        }elseif($request->key == "policy"){
            $kkuljaem_info = Models\KkuljaemInfo::select(['key','value'])->where('key','policy')->first();
        }elseif($request->key == "version"){
            $res = [];
            $res[0] = Models\KkuljaemInfo::select(['key','value'])->where('key','ios_ver')->first();
            $res[1] = Models\KkuljaemInfo::select(['key','value'])->where('key','and_ver')->first();
            $kkuljaem_info = $res;
        }elseif($request->key == "about"){
            $kkuljaem_info = Models\KkuljaemInfo::select(['key','value'])->where('key','about')->first();
        }

        return response()->json([
            'message'=>'Success',
            'data'=> $kkuljaem_info
    ]);
    }
}
