<?php

namespace App\Http\Controllers\Reference;

use App\Models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\Helper;

class ReferenceController extends Controller
{
    public function addData(Request $request)
    {
        $validation = new Helper\ValidationController('reference');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        $uuid = $validation->data['uuid'];

        $data = [
            'nama'                  => request('nama'),
            'kode'                  => request('kode'),
            'tgl_aktif'             => request('tgl_aktif'),
            'status'                => request('status'),
            'uuid'                  => $uuid
        ];

        $input = new Helper\InputController('reference',$data);


        return response()->json(['message'=>'Success','info'
        => 'Proses Input Berhasil']);
    }

    public function updateData(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $validation = new Helper\ValidationController('reference');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;


        $class_cat = Models\Reference::where('uuid',$uuid)->first();

        if(!$class_cat){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;

        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }
        
        $data = [
            'nama'                  => request('nama'),
            'kode'                  => request('kode'),
            'tgl_aktif'             => request('tgl_aktif'),
            'status'                => request('status'),
            'uuid'                  => $uuid
        ];

        $input = new Helper\UpdateController('reference',$data);

        return response()->json(['message'=>'Success','info'
        => 'Proses Update Berhasil']);
    }

    public function deleteData(Request $request){
        $result = [];
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        
        $post = Models\Reference::where('uuid',$uuid)->get();
        if(count($post)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        #delete comment
        $delete = Models\Reference::where('uuid',$uuid)->delete();
    

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'info'    => 'Proses Hapus Reference Berhasil'
        ]);
    }

    public function allData(Request $request){

        
        $class_cat = Models\Reference::select([
            'nama',
            'kode',
            'tgl_aktif',
            'status',
            'uuid',
            ])->where('status','1')->get();
        

        return response()->json(['message'=>'Success','data'
        => $class_cat]);
    }

    public function allDataStudent(Request $request){
        $result = [];
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        
        $post = Models\Reference::where('uuid',$uuid)->get();
        if(count($post)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        
        $idReference = Models\Reference::select([
            'id',
            ])->get();
        $subs = Models\Subs::whereIn('id_reference',$idReference)->get();
        $result = [];
        if(count($subs)>0){
            $result['nama'] = $subs[0]->reference->nama;
            $result['kode'] = $subs[0]->reference->kode;
            $result['reference_uuid'] = $uuid;
            $result['jml_subs'] = count($subs);
            $arr0 = [];
            for($i=0;$i<count($subs);$i++){
                
                $status = 'TUNGGU';
                if($subs[$i]->subs_status=='PAID'){
                    $status = 'BERHASIL';
                }if(date_format(date_create($subs[$i]->tgl_akhir_bayar),"Y/m/d H:i:s") < date('Y/m/d H:i:s')){
                    $status = 'GAGAL';
                }
                $arr = [
                    'nama'=>$subs[$i]->user->nama,
                    'subs_status'=>$status,
                    'user_uuid'=>$subs[$i]->user->uuid,
                ];
                $arr0[$i] = $arr;
            }
            $result['subscriber'] = $arr0;
        }
        

        return response()->json(['message'=>'Success','data'
        => $result]);
    }

    public function allDatas(Request $request){

        $class_cat = Models\Reference::select([
            'nama',
            'kode',
            'tgl_aktif',
            'status',
            'uuid',
            ])->where('status','!=',3)->get();
        

        return response()->json(['message'=>'Success','data'
        => $class_cat]);
    }

    public function detailData(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        if(count(Models\Reference::where('uuid',$uuid)->get())==0){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $class_cat = Models\Reference::where('uuid',$uuid)->first();
        unset($class_cat['id']);

        return response()->json(['message'=>'Success','data'
        => $class_cat]);
    }
}
