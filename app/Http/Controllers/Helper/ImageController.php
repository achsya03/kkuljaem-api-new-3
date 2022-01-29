<?php

namespace App\Http\Controllers\Helper;

use App\Models;
use App\Http\Controllers\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function addNonMemberImage(Request $request){
        
        if(!$request->non_mem_image){
            return response()->json(['message'=>'Failed','info'=>"Image harus diisi"]);

        }
        $validation = new Helper\ValidationController('kkuljaemInfo');
        if($request->non_mem_image){
            $gambar1 = $request->non_mem_image;
            $uploadedFileUrl1 = $validation->UUidCheck($gambar1,'KkuljaemInfo/NonMem');

            $rnd = Str::random(5);
            $img = Models\KkuljaemInfo::create([
                'key'           => 'Non-Mem-Image'.$rnd,
                'value'         => $uploadedFileUrl1['getSecurePath'],
            ]);
            $id = Models\KkuljaemInfo::create([
                'key'           => 'Non-Mem-Id'.$rnd,
                'value'         => $uploadedFileUrl1['getPublicId'],
            ]);

            //$input = new Helper\InputController('theme',$data);
        }

        return response()->json(['message'=>'Success','info'
        => 'Proses Input Berhasil']);
    }
    public function editNonMemberImage(Request $request){

    }
    public function deleteNonMemberImage(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message' => 'Failed',
            'info'=>"Token Tidak Sesuai"]);
        }
        if(count($img = KkuljaemInfo::where('key',$uuid)
                ->get())==0){
            return response()->json(['message' => 'Failed',
            'info'=>"Token Tidak Sesuai"]);
        }
        $img = Models\KkuljaemInfo::where('key',$uuid)->delete();
        $id = Models\KkuljaemInfo::where('key','LIKE','%',substr($uuid,-5),'%')->delete();

        return response()->json([
                'message'=>'Success',
                'info'  => 'Proses Delete Image Berhasil']);
    }
    public function getAllNonMemberImage(Request $request){
        $img = Models\KkuljaemInfo::where('key','LIKE','%Non-Mem-Image%')->orderBy('key','ASC')
                ->get();
        foreach ($img as $im) {
            unset($im['id']);  
        }     

        return response()->json(['message'=>'Success','data'
        => $img]);
    }
    public function getDetailNonMemberImage(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message' => 'Failed',
            'info'=>"Token Tidak Sesuai"]);
        }
        
        if(count($img = KkuljaemInfo::where('key',$uuid)
                ->get())==0){
            return response()->json(['message' => 'Failed',
            'info'=>"Token Tidak Sesuai"]);
        }

        $img = KkuljaemInfo::where('key',$uuid)
                ->first();
        unset($img['id']);

        return response()->json([
            'message'=>'Success',
            'data' => $img
        ]);
    }
}
