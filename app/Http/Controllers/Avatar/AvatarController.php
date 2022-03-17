<?php

namespace App\Http\Controllers\Avatar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models;
use Validator;
use Hash;
use Session;
use Cloudinary;

class AvatarController extends Controller
{
    
    public function __construct(Request $request){
        $this->middleware('auth');
    }

    public function getAllAvatarGroup(Request $request){
        $avatarGroup = Models\AvatarGroup::select('nama','deskripsi','uuid')
                                            ->get();
        return response()->json([
            'message' => 'Success',
            'data' => $avatarGroup
        ]);
    }

    public function getDetailAvatarGroup(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
    }

    public function addAvatarGroup(Request $request){
        
    }

    public function editAvatarGroup(Request $request){
        
    }

    public function deleteAvatarGroup(Request $request){
        
    }

    public function getAvatarByGroup(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        if(!$idGroup=Models\AvatarGroup::where('uuid',$request->uuid)->first){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        $avatar = Models\Avatar::select('nama','deskripsi','avatar_url','avatar_id','uuid')
                                ->where('id_avatar_group',$idGroup->id)
                                ->get();
        $result = [
            'group_name'=>$idGroup->nama,
            'group_desc'=>$idGroup->deskripsi,
            'group_uuid'=>$idGroup->uuid,
            'avatars'=>$avatar,
        ];
        
        return response()->json([
            'message' => 'Success',
            'data' => $result
        ]);
    }

    public function getDetailAvatar(Request $request){

    }

    public function addAvatar(Request $request){
        
    }

    public function editAvatar(Request $request){
        
    }

    public function deleteAvatar(Request $request){
        
    }

    public function editUserAvatar(Request $request){
        
    }
}
