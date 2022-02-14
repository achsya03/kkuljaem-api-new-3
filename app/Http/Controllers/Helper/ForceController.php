<?php

namespace App\Http\Controllers\Helper;

use App\Models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use General;
use Illuminate\Support\Str;
use App\Http\Controllers\Helper;
use App\Http\Controllers\Notification;
use App\Http\Controllers\FCMController;

class ForceController extends Controller
{
    public function __construct(Request $request){
        $this->middleware('auth');
    }
    public function getAllLog(Request $request){
        $img = Models\ForceLog::all();

        foreach ($img as $im) {
            $ad = Models\DetailMentor::where('id',$im['id_detail_mentor'])->first();
            $st = Models\DetailStudent::where('id',$im['id_detail_student'])->first();
            $im['admin'] = $ad->user->nama;
            $im['student'] = $st->user->nama;
            unset($im['id_detail_mentor']);  
            unset($im['id_detail_student']);  
            unset($im['id']);  
        }     

        return response()->json(['message'=>'Success','data'
        => $img]);
    }
    public function forceSubs(Request $request){
        $result = [];
        if($request->user()->jenis_pengguna != 2){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Anda bukan admin'
            ]);
        }
        // if(!$token = $request->token){
        //     return response()->json([
        //         'message' => 'Failed',
        //         'error' => 'Token tidak sesuai'
        //     ]);
        // }
        // if($token != date("Y__m__")){
        //     return response()->json([
        //         'message' => 'Failed',
        //         'error' => 'Format token tidak sesuai'
        //     ]);
        // }
        if(!$email = $request->email){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Email tidak sesuai'
            ]);
        }  
        if(!$note = $request->note){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Note tidak sesuai'
            ]);
        }  
        if(count($users = Models\User::where('email',$email)->get())==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Email tidak sesuai'
            ]);
        }
        $tgl_awal = $users[0]->tgl_langganan_akhir;
        if(date_format(date_create($request->tgl_akhir),"Y/m/d")<date("Y/m/d")){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Tanggal Akhir Harus Lebih Dari Hari ini'
            ]);
        }
        $tgl_akhir = date_format(date_create($request->tgl_akhir),"Y/m/d");
        $tgl_akh = (new \DateTime(date('Y-m-d')))->modify('+'.(30*1).' day')->format('Y-m-d');
            
        $mntr = Models\DetailMentor::where('id_users', $request->user()->id)->get();
        if(count(Models\DetailStudent::where('id_users', $users[0]->id)->get())<1){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Data siswa belum lengkap'
            ]);
        }

        $user = Models\User::where('email',$email)->update([
            'tgl_langganan_akhir' => $tgl_akhir
        ]);
        $force_log = Models\ForceLog::create([
            "id_detail_student" => $users[0]->detailStudent[0]->id,
            "id_detail_mentor" => $mntr[0]->id,
            "note" => $note,
            "tgl_awal" => $tgl_awal,
            "tgl_edit" => $tgl_akhir,
            "uuid" => Str::random(144)
        ]);

        $result = [
            'email' => $email,
            'tgl_langganan_awal' => $tgl_awal,
            'tgl_langganan_akhir' => $tgl_akhir
        ];

        return response()->json([
			'message' => 'Success',
			'info' => 'Proses Update Berhasil',
			'data' => $result
		]);
    }

    public function forceNotif(Request $request){
        $limit = $request->limit;
        $page = $request->page;
        $user = Models\User::select('nama','device_id','uuid')
                    ->whereNotNull('device_id')
                    ->where('device_id','!=','web')
                    ->limit($limit)->offset(($page - 1) * $limit)
                    //->where('email','ach.sya03@gmail.com')
                    ->orderBy('id','ASC')
                    ->get();
        //return $user;
        
        $validation = new Helper\ValidationController('notification');
        $counter = 0;
        $datas = [];
        $arr = array();
        for($i=0;$i<count($user);$i++){
            
            $datas = [
                // 'i' => $i,
                'user_uuid'       => $user[$i]->uuid,
                'judul'           => $request->judul,
                'keterangan'       => $request->deskripsi,
                'posisi'          => 'Notifikasi',
                //'gambar'          => $datas['gambar'],
                'uuid_target'     => '#',
                'tgl_notif'        => date('Y-m-d h:i:s'),
                'status'          => 0,
                'maker_uuid'     => $request->user()->uuid,
                'uuid'            => $validation->data['uuid'],
            ];
            //array_push($arr,$user[$i]->device_id);
            //print_r($datas);

            $push_notif = FCMController::sendNotification($user[$i]->user,$datas);

            $counter = $i;
        }
        
        Models\Notification::insert($datas);
        //$push_notif = FCMController::sendLotNotification(json_encode($arr),$request->judul,$request->deskripsi);


        return response()->json([
			'message' => 'Success',
			'info' => 'Proses Force Notif Berhasil Dengan '.($counter+1).' Data',
            //'stat push' => $push_notif
		]);
    }

    public function forceWordUrl(Request $request){
        $result = [];
        $word = Models\Words::orderBy('id','ASC')->get();

        $aa = [];
        $bb = [];

        for($i=0;$i<count($word);$i++){
            if(substr($word[$i]->url_pengucapan, 0, 11) == 'https://res'){
                // $aa[$i] = substr($word[$i]->url_pengucapan, 0, 11);
                // $bb[$i] = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($word[$i]->url_pengucapan, 69);
                $url = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($word[$i]->url_pengucapan, 69);
                $aa[$i] = $word[$i]->id;
                $update = Models\Words::where('id',$word[$i]->id)
                    ->update([
                        'url_pengucapan' => $url
                    ]);
            }
        }

        return response()->json([
			'message' => 'Success',
			'info' => 'Proses Update Berhasil',
		]);
    }

    public function forceWordPath(Request $request){
        $result = [];
        $word = Models\Words::orderBy('id','ASC')->get();

        $aa = [];
        $bb = [];

        for($i=0;$i<count($word);$i++){
            if(substr($word[$i]->pengucapan_id, 0, 7) == 'Testing'){
                // $aa[$i] = substr($word[$i]->url_pengucapan, 0, 11);
                // $bb[$i] = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($word[$i]->url_pengucapan, 69);
                $url = substr($word[$i]->pengucapan_id, 8);
                $aa[$i] = $word[$i]->id;
                $update = Models\Words::where('id',$word[$i]->id)
                    ->update([
                        'pengucapan_id' => $url
                    ]);
            }
        }

        return response()->json([
			'message' => 'Success',
			'info' => 'Proses Update Berhasil',
		]);
    }

    public function forceQuestionPath(Request $request){
        $result = [];
        $word = Models\Question::orderBy('id','ASC')->get();

        $aa = [];
        $bb = [];

        for($i=0;$i<count($word);$i++){
            if(substr($word[$i]->gambar_id, 0, 7) == 'Testing'){
                // $aa[$i] = substr($word[$i]->url_pengucapan, 0, 11);
                // $bb[$i] = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($word[$i]->url_pengucapan, 69);
                $url = substr($word[$i]->gambar_id, 8);
                $aa[$i] = $word[$i]->id;
                $update = Models\Question::where('id',$word[$i]->id)
                    ->update([
                        'gambar_id' => $url
                    ]);
            }
        }
        for($i=0;$i<count($word);$i++){
            if(substr($word[$i]->file_id, 0, 7) == 'Testing'){
                // $aa[$i] = substr($word[$i]->url_pengucapan, 0, 11);
                // $bb[$i] = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($word[$i]->url_pengucapan, 69);
                $url = substr($word[$i]->file_id, 8);
                $aa[$i] = $word[$i]->id;
                $update = Models\Question::where('id',$word[$i]->id)
                    ->update([
                        'file_id' => $url
                    ]);
            }
        }

        return response()->json([
			'message' => 'Success',
			'info' => 'Proses Update Berhasil',
		]);
    }

    public function forcePostUrl(Request $request){
        $result = [];
        $post_image = Models\PostImage::orderBy('id','ASC')->get();

        $aa = [];
        $bb = [];

        for($i=0;$i<count($post_image);$i++){
            if(substr($post_image[$i]->url_gambar, 0, 11) == 'https://res'){
                // $aa[$i] = substr($word[$i]->url_pengucapan, 0, 11);
                // $bb[$i] = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($word[$i]->url_pengucapan, 69);
                $url = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($post_image[$i]->url_gambar, 69);
                $aa[$i] = $post_image[$i]->id;
                $update = Models\PostImage::where('id',$post_image[$i]->id)
                    ->update([
                        'url_gambar' => $url
                    ]);
            }
        }

        return response()->json([
			'message' => 'Success',
			'info' => 'Proses Update Berhasil',
		]);
    }

    public function forceBannerUrl(Request $request){
        $result = [];
        $banner = Models\Banner::orderBy('id','ASC')->get();

        $aa = [];
        $bb = [];

        for($i=0;$i<count($banner);$i++){
            if(substr($banner[$i]->url_web, 0, 11) == 'https://res'){
                // $aa[$i] = substr($word[$i]->url_pengucapan, 0, 11);
                // $bb[$i] = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($word[$i]->url_pengucapan, 69);
                $url1 = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($banner[$i]->url_web, 69);
                $url2 = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($banner[$i]->url_mobile, 69);
                $aa[$i] = $banner[$i]->id;
                $update = Models\Banner::where('id',$banner[$i]->id)
                    ->update([
                        'url_web' => $url1,
                        'url_mobile' => $url2,
                    ]);
            }
        }

        return response()->json([
			'message' => 'Success',
			'info' => 'Proses Update Berhasil',
		]);
    }


    public function forceMentorDetailUrl(Request $request){
        $result = [];
        $detail_mentor = Models\DetailMentor::orderBy('id','ASC')->get();

        $aa = [];
        $bb = [];

        for($i=0;$i<count($detail_mentor);$i++){
            if(substr($detail_mentor[$i]->url_foto, 0, 11) == 'https://res'){
                // $aa[$i] = substr($word[$i]->url_pengucapan, 0, 11);
                // $bb[$i] = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($word[$i]->url_pengucapan, 69);
                $url = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com/Profile'.substr($detail_mentor[$i]->url_foto, 69);
                $aa[$i] = $detail_mentor[$i]->id;
                $update = Models\DetailMentor::where('id',$detail_mentor[$i]->id)
                    ->update([
                        'url_foto' => $url
                    ]);
            }
        }

        return response()->json([
			'message' => 'Success',
			'info' => 'Proses Update Berhasil',
		]);
    }

    public function forceClassesUrl(Request $request){
        $result = [];
        $classes = Models\Classes::orderBy('id','ASC')->get();

        $aa = [];
        $bb = [];

        for($i=0;$i<count($classes);$i++){
            if(substr($classes[$i]->url_web, 0, 11) == 'https://res'){
                // $aa[$i] = substr($word[$i]->url_pengucapan, 0, 11);
                // $bb[$i] = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($word[$i]->url_pengucapan, 69);
                $url1 = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($classes[$i]->url_web, 69);
                $url2 = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($classes[$i]->url_mobile, 69);
                $aa[$i] = $classes[$i]->id;
                $update = Models\Classes::where('id',$classes[$i]->id)
                    ->update([
                        'url_web' => $url1,
                        'url_mobile' => $url2,
                    ]);
            }
        }

        return response()->json([
			'message' => 'Success',
			'info' => 'Proses Update Berhasil',
		]);
    }

    public function forceQuestionUrl(Request $request){
        $result = [];
        $question = Models\Question::orderBy('id','ASC')->get();

        $aa = [];
        $bb = [];

        for($i=0;$i<count($question);$i++){
            if(substr($question[$i]->url_gambar, 0, 11) == 'https://res'){
                // $aa[$i] = substr($word[$i]->url_pengucapan, 0, 11);
                // $bb[$i] = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($word[$i]->url_pengucapan, 69);
                $url1 = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($question[$i]->url_gambar, 69);
                $aa[$i] = $question[$i]->id;
                $update = Models\Question::where('id',$question[$i]->id)
                    ->update([
                        'url_gambar' => $url1,
                    ]);
            }
            if(substr($question[$i]->url_file, 0, 11) == 'https://res'){
                // $aa[$i] = substr($word[$i]->url_pengucapan, 0, 11);
                // $bb[$i] = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($word[$i]->url_pengucapan, 69);
                $url1 = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($question[$i]->url_file, 69);
                $aa[$i] = $question[$i]->id;
                $update = Models\Question::where('id',$question[$i]->id)
                    ->update([
                        'url_file' => $url1,
                    ]);
            }
        }

        return response()->json([
			'message' => 'Success',
			'info' => 'Proses Update Berhasil',
		]);
    }

    public function forceOptionUrl(Request $request){
        $result = [];
        $option = Models\Option::orderBy('id','ASC')->get();

        $aa = [];
        $bb = [];

        for($i=0;$i<count($option);$i++){
            if(substr($option[$i]->url_gambar, 0, 11) == 'https://res'){
                // $aa[$i] = substr($word[$i]->url_pengucapan, 0, 11);
                // $bb[$i] = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($word[$i]->url_pengucapan, 69);
                $url1 = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($option[$i]->url_gambar, 69);
                $aa[$i] = $option[$i]->id;
                $update = Models\Option::where('id',$option[$i]->id)
                    ->update([
                        'url_gambar' => $url1,
                    ]);
            }
            if(substr($option[$i]->url_file, 0, 11) == 'https://res'){
                // $aa[$i] = substr($word[$i]->url_pengucapan, 0, 11);
                // $bb[$i] = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($word[$i]->url_pengucapan, 69);
                $url1 = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($option[$i]->url_file, 69);
                $aa[$i] = $option[$i]->id;
                $update = Models\Option::where('id',$option[$i]->id)
                    ->update([
                        'url_file' => $url1,
                    ]);
            }
        }

        return response()->json([
			'message' => 'Success',
			'info' => 'Proses Update Berhasil',
		]);
    }
}
