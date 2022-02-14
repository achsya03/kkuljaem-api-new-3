<?php

namespace App\Http\Controllers\Notification;

use App\Models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Helper;
use Validator;

class NotificationController extends Controller
{
    public static function addData($datas){
        $validation = new Helper\ValidationController('notification');
        $ket = '';
        if(isset($datas['deskripsi'])){
            $ket = $datas['deskripsi'];
        }elseif(isset($datas['keterangan'])){
            $ket = $datas['keterangan'];
        }

        $data = [
			'user_uuid'       => $datas['user_uuid'],
			'judul'           => $datas['judul'],
			'keterangan'       => $ket,
			'posisi'          => $datas['posisi'],
			//'gambar'          => $datas['gambar'],
            'tgl_notif'        => date('Y-m-d h:i:s'),
            'status'          => 0,
            'uuid_target'     => $datas['uuid_target'],
            'maker_uuid'     => $datas['maker_uuid'],
			'uuid'            => $validation->data['uuid'],
        ];

        Models\Notification::create([
			'user_uuid'       => $data['user_uuid'],
			'judul'           => $data['judul'],
			'keterangan'       => $data['keterangan'],
			'posisi'          => $data['posisi'],
			//'gambar'          => $data['gambar'],
            'tgl_notif'        => $data['tgl_notif'],
            'status'          => $data['status'],
            'uuid_target'     => $data['uuid_target'],
            'maker_uuid'     => $data['maker_uuid'],
			'uuid'            => $data['uuid'],
        ]);

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'info'    => 'Proses Input Notifikasi Berhasil'
        ]);
    }
    public function updatePush(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        if(count(Models\Notification::where('uuid',$uuid)->get())==0){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        Models\Notification::where('uuid',$uuid)->update([
            'status'          => 1,
			'uuid'            => $uuid,
        ]);

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'info'    => 'Proses Update Notifikasi Berhasil'
        ]);
    }
    public function updateRead(Request $request){

        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        if(count(Models\Notification::where('uuid',$uuid)->get())==0){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        Models\Notification::where('uuid',$uuid)->update([
            'status'          => 2,
			'uuid'            => $uuid,
        ]);

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'info'    => 'Proses Update Notifikasi Berhasil'
        ]);
    }

    public function deleteData(Request $request){

        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        if(count(Models\Notification::where('uuid',$uuid)->get())==0){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        Models\Notification::where('uuid',$uuid)->delete();

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'info'    => 'Proses Delete Notifikasi Berhasil'
        ]);
    }
    public function getData(Request $request){
        if(!$user = $request->user()){
            return response()->json(['message'=>'Unauthenticated']);
        }
        $co_notif = Models\Notification::where('user_uuid',$user->uuid)
                    ->orderBy('status','ASC')->orderBy('tgl_notif','DESC')->get();

        
        $res = [];
        $co_not_read = 0;
        for($i=0;$i<count($co_notif);$i++){
            $stat = 'unread';
            if($co_notif[$i]->status != 0){
                $stat = 'readed';
            }

            $maker_uuid = $co_notif[$i]->maker_uuid;
            $user1 = Models\User::where('uuid',$maker_uuid)->first();
            $st_user = ["new user","data complete","member data complete","admin-mentor"];
            if($user1->jenis_pengguna=='0') {
                if($user1->jenis_akun=='0'){
                    $st_user_post = $st_user[0];
                }elseif($user1->jenis_akun==2){
                    $st_user_post = $st_user[1];
                }
            }
            if(date_format(date_create($user1->tgl_langganan_akhir),"Y/m/d") >= date('Y/m/d')) {
                $st_user_post = $st_user[2];
            }
            if($user1->jenis_pengguna==1 || $user1->jenis_pengguna==2) {
                $st_user_post = $st_user[3];
            }



            $res[$i] = [
                'judul' => $co_notif[$i]->judul,
                'deskripsi' => $co_notif[$i]->keterangan,
                'nama_pengirim' => $user1->nama,
                'stat_pengirim' => $st_user_post,
                'posisi' => $co_notif[$i]->posisi,
                'tgl_notif' => $co_notif[$i]->tgl_notif,
                'status' => $stat,
                'uuid_target' => $co_notif[$i]->uuid_target,
                'uuid_notif' => $co_notif[$i]->uuid,
            ];

            if($user1->url_foto != null && $user1->jenis_pengguna != '0'){
                $res[$i] += [
                    'foto_pengirim' => $user1->url_foto,
                ];
            }

            $det_student = Models\DetailStudent::where('id_users',$user1->id)->get();
            if(count($det_student)>0){
                $res[$i]['jenis_kelamin'] = $det_student[0]->jenis_kel;

            }

            if($co_notif[$i]->status==0){
                $co_not_read += 1;
            }
        }
        $result['number_unread_notif'] = $co_not_read;
        $result['list_notif'] = $res;
        $result['user_uuid'] = $user->uuid;

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'data'    => $result 
        ]);
    }
    public function getCount(Request $request){
        $user = $request->user();
        $co_notif = Models\Notification::where('user_uuid',$user->uuid)->get();

        $result['jml_notif'] = count($co_notif);
        $result['user_uuid'] = $user->uuid;

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'data'    => $result 
        ]);
    }
}
