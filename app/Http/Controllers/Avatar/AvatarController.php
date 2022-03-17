<?php

namespace App\Http\Controllers\Avatar;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper;
use Illuminate\Http\Request;
use App\Models;
use Validator;
use Hash;
use Session;
use Cloudinary;

class AvatarController extends Controller
{
    
    // public function __construct(Request $request){
    //     $this->middleware('auth');
    // }

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
        if(!$idGroup=Models\AvatarGroup::where('uuid',$request->token)->first()){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        $avatar = Models\Avatar::select('nama','deskripsi','avatar_url','uuid')
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
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        if(!$idAvatar=Models\Avatar::where('uuid',$request->token)->first()){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $detailStudent = Models\DetailStudent::where('id_users',$request->user()->id)->first();

        if(!($avatarStudent = Models\AvatarStudent::where('id_avatar',$idAvatar->id)
                                        ->where('id_detail_student',$detailStudent->id)
                                        ->first())){

            $validation = new Helper\ValidationController('avatarStudent');
            $uuid1 = $validation->data['uuid'];

            $data = [
                'id_avatar'             => $idAvatar->id,
                'id_detail_student'     => $detailStudent->id,
                'uuid'                  => $uuid1
            ];

            $result = new Helper\InputController('avatarStudent',$data);
        }else{

            $result = Models\AvatarStudent::where('id_detail_student',$detailStudent->id)
                                        ->update([
                                            'id_avatar'=>$idAvatar->id
                                        ]);
        }
        
        if($result){
            return response()->json([
                'message' => 'Success',
                'data' => $result
            ]);
        }else{
            return response()->json([
                'message' => 'Success',
                'error' => 'Proses gagal dilakukan'
            ]);
        }
        
    }
}
