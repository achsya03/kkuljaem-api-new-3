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
use Illuminate\Support\Facades\Storage;

class AvatarController extends Controller
{
    
    // public function __construct(Request $request){
    //     $this->middleware('auth');
    // }

    public function getAllAvatarGroup(Request $request){
        $avatarGroup = Models\AvatarGroup::select('nama','deskripsi','uuid')
                                            ->orderBy('nama','ASC')
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

        if(!$idGroup=Models\AvatarGroup::select('nama','deskripsi','uuid')->where('uuid',$request->token)->first()){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        unset($idGroup['id']);

        return response()->json([
            'message' => 'Success',
            'data' => $idGroup
        ]);
    }

    public function addAvatarGroup(Request $request){
        $validation = new Helper\ValidationController('avatarGroup');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        $uuid1 = $validation->data['uuid'];

        $data = [
            'nama'             => $request->nama,
            'deskripsi'        => $request->deskripsi,
            'uuid'             => $uuid1
        ];

        $result = new Helper\InputController('avatarGroup',$data);

        if($result){
            return response()->json([
                'message' => 'Success',
                'data' => 'Input data berhasil dilakukan'
            ]);
        }else{
            return response()->json([
                'message' => 'Failed',
                'data' => 'Input data gagal dilakukan'
            ]);
        }
    }

    public function editAvatarGroup(Request $request){

        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        if(!$idGroup=Models\AvatarGroup::select('nama','deskripsi','uuid')->where('uuid',$request->token)->first()){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $validation = new Helper\ValidationController('avatarGroup');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        $data = [
            'nama'             => $request->nama,
            'deskripsi'        => $request->deskripsi,
            'uuid'             => $request->token
        ];

        $result = new Helper\UpdateController('avatarGroup',$data);

        if($result){
            return response()->json([
                'message' => 'Success',
                'data' => 'Update data berhasil dilakukan'
            ]);
        }else{
            return response()->json([
                'message' => 'Failed',
                'data' => 'Update data gagal dilakukan'
            ]);
        }
    }

    public function deleteAvatarGroup(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        if(!$result=Models\AvatarGroup::where('uuid',$request->token)->delete()){
            return response()->json([
                'message' => 'Failed',
                'data' => 'Data gagal dihapus'
            ]);
        }else{
            return response()->json([
                'message' => 'Success',
                'data' => 'Data berhasil dihapus'
            ]);
        }

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
                                ->orderBy('nama','ASC')
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
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        if(!$idAvatar=Models\Avatar::select('avatar.nama as nama','avatar.deskripsi','avatar_url','avatar_group.uuid AS group_uuid','avatar.uuid')
                                    ->rightJoin('avatar_group','avatar.id_avatar_group','=','avatar_group.id')
                                    ->where('avatar.uuid',$request->token)->first()){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        
        unset($idAvatar['id']);
        unset($idAvatar['id_avatar_group']);
        unset($idAvatar['avatar_id']);

        return response()->json([
            'message' => 'Success',
            'data' => $idAvatar
        ]);
    }

    public function addAvatar(Request $request){
        $validation = new Helper\ValidationController('avatar');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        if(!$idGroup=Models\AvatarGroup::select('id')->where('uuid',$request->group_uuid)->first()){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $uuid1 = $validation->data['uuid'];
        $uploadedFileUrl1 = $validation->UUidCheck($request->avatar_image,'Avatar');

        $data = [
            'nama'             => $request->nama,
            'deskripsi'        => $request->deskripsi,
            'id_avatar_group'  => $idGroup->id,
            'avatar_url'       => $uploadedFileUrl1['getSecurePath'],
            'avatar_id'        => $uploadedFileUrl1['getPublicId'],
            'uuid'             => $uuid1
        ];

        $result = new Helper\InputController('avatar',$data);

        if($result){
            return response()->json([
                'message' => 'Success',
                'data' => 'Input data berhasil dilakukan'
            ]);
        }else{
            return response()->json([
                'message' => 'Failed',
                'data' => 'Input data gagal dilakukan'
            ]);
        }
    }

    public function editAvatar(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        if(!$idAvatar=Models\Avatar::select('nama','deskripsi','id_avatar_group AS group_id','uuid')->where('uuid',$request->token)->first()){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        if(!$idGroup=Models\AvatarGroup::select('id')->where('uuid',$request->group_uuid)->first()){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $validation = new Helper\ValidationController('avatarEdit');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        $uploadedFileUrl1 = [
            'getSecurePath'=>'',
            'getPublicId'=>''
        ];

        if(isset($request->avatar_image) && $request->avatar_image != NULL){
            $uploadedFileUrl1 = $validation->UUidCheck($request->avatar_image,'Avatar');
        }

        $data = [
            'nama'             => $request->nama,
            'deskripsi'        => $request->deskripsi,
            'id_avatar_group'  => $idGroup->id,
            'avatar_url'       => $uploadedFileUrl1['getSecurePath'],
            'avatar_id'        => $uploadedFileUrl1['getPublicId'],
            'uuid'             => $request->token
        ];

        $result = new Helper\UpdateController('avatar',$data);

        if($result){
            return response()->json([
                'message' => 'Success',
                'data' => 'Update data berhasil dilakukan'
            ]);
        }else{
            return response()->json([
                'message' => 'Failed',
                'data' => 'Update data gagal dilakukan'
            ]);
        }
        
    }

    public function deleteAvatar(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        if(!$idAvatar=Models\Avatar::select('avatar_id')->where('uuid',$request->token)->first()){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        if($result=Models\Avatar::where('uuid',$request->token)->delete() and Storage::disk('do_spaces')->delete($idAvatar->avatar_id)){
            return response()->json([
                'message' => 'Success',
                'data' => 'Data berhasil dihapus'
            ]);
        }else{
            return response()->json([
                'message' => 'Failed',
                'data' => 'Data gagal dihapus'
            ]);
        }
    }

    public function editUserAvatar(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        if(!$idAvatar=Models\Avatar::where('uuid',$request->token)->first()){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $detailStudent = Models\DetailStudent::where('id_users',$request->user()->id)->first();

        if(!$detailStudent){
            $validation = new Helper\ValidationController('userStudentAvatar');
            $uuid = $validation->data['uuid'];

            Models\DetailStudent::create([
                'id_users'           => $request->user()->id,
                'uuid'               => $uuid
            ]);

            $detailStudent = Models\DetailStudent::select('id')->where('uuid',$uuid)->first();
        }
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
                'data' => 'Proses berhasil dilakukan'
            ]);
        }else{
            return response()->json([
                'message' => 'Failed',
                'error' => 'Proses gagal dilakukan'
            ]);
        }
        
    }
}
