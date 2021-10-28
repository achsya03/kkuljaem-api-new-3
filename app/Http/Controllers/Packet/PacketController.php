<?php

namespace App\Http\Controllers\Packet;

use App\Models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Helper;
use Validator;

class PacketController extends Controller
{
    public function addData(Request $request)
    {
        $validation = new Helper\ValidationController('packet');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        $uuid = $validation->data['uuid'];

        $data = [
            'lama_paket'        => request('lama_paket'),
            'harga'             => request('harga'),
            'status_aktif'      => request('status_aktif'),
            'uuid'              => $uuid
        ];

        $input = new Helper\InputController('packet',$data);


        return response()->json(['message'=>'Success','info'
        => 'Proses Input Berhasil']);
    }

    public function updateData(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $validation = new Helper\ValidationController('packet');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;


        $class_cat = Models\Packet::where('uuid',$uuid)->first();

        if(!$class_cat || $class_cat == ''){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;

        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }
        
        $data = [
            'lama_paket'        => request('lama_paket'),
            'harga'             => request('harga'),
            'status_aktif'      => request('status_aktif'),
            'uuid'              => $uuid
        ];

        $input = new Helper\UpdateController('packet',$data);

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
        
        $post = Models\Packet::where('uuid',$uuid)->get();
        if(count($post)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        if($post[0]->status_aktif==1){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Packet Masih Aktif'
            ]);
        }

        #delete comment
        $delete = Models\Packet::where('uuid',$uuid)->delete();
    

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'info'    => 'Proses Hapus Paket Berhasil'
        ]);
    }

    public function allData(Request $request){

        $class_cat = Models\Packet::select([
            'lama_paket',
            'harga',
            'status_aktif',
            'uuid',
            ])->where('status_aktif','1')->get();
        

        return response()->json(['message'=>'Success','data'
        => $class_cat]);
    }

    public function allDatas(Request $request){

        $class_cat = Models\Packet::select([
            'lama_paket',
            'harga',
            'status_aktif',
            'uuid',
            ])->get();
        

        return response()->json(['message'=>'Success','data'
        => $class_cat]);
    }

    public function detailData(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        if(count(Models\Packet::where('uuid',$uuid)->get())==0){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $class_cat = Models\Packet::select([
            'lama_paket',
            'harga',
            'status_aktif',
            'uuid',
            ])->where('uuid',$uuid)->first();
        //unset($class_cat['id']);

        return response()->json(['message'=>'Success','data'
        => $class_cat]);
    }

    public function detailSelect(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        if(count(Models\Packet::where('uuid',$uuid)->get())==0){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        $ref=Models\Referal::where('kode',$request->referal)->first();

        $packet = Models\Packet::select([
            'lama_paket',
            'harga',
            'status_aktif',
            'uuid',
            ])->where('uuid',$uuid)->first();
        //unset($class_cat['id']);
        $orderDate = date('Y-m-d');		
		$paymentDue = (new \DateTime($orderDate))->modify('+'.(30*$packet->lama_paket).' day')->format('Y-m-d');
        $result['tgl_daftar'] = $orderDate;
        $result['tgl_akhir'] = $paymentDue;
        $result['kode_referal'] = $ref->kode;
        $result['nama_referal'] = $ref->nama;
        $result['packet'] = $packet;

        return response()->json(['message'=>'Success','data'
        => $result]);
    }
}
