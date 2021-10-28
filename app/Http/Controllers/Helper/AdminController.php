<?php

namespace App\Http\Controllers\Helper;

use App\Models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth;

use App\Http\Controllers\Banner;

use App\Http\Controllers\Classes;

use App\Http\Controllers\Helper;
use App\Http\Controllers\Payment;

class AdminController extends Controller
{
    public function __construct(Request $request){
        $this->middleware('auth');
    }
    
    public function dashboard(Request $request){
        if(!$uuidUser = $request->user()){
            return response()->json([
                'message' => 'Failed',
                'data'    => 'Dimohon Untuk Login Terlebih Dahulu'
            ]);
        }
        if($request->user()->jenis_pengguna != 1 and
            $request->user()->jenis_pengguna != 2){
            return response()->json([
                'message' => 'Failed',
                'data'    => 'Jenis Pengguna Tidak Sesuai'
            ]);
        }//$date = date_format(date_create($usr->tgl_langganan_akhir),"Y/m/d");

        $result = [];
        $jmlSiswa = count(Models\DetailStudent::all());
        $jmlSubs = count(Models\User::where('jenis_pengguna',0)
                        ->where('tgl_langganan_akhir','>=',date('Y/m/d'))
                        ->get());
        $jmlMentor = count(Models\User::where('jenis_pengguna',1)
                        ->get());
        $jmlClass = count(Models\Classes::where('status_tersedia',1)
                        ->get());
        $jmlQnA = count(Models\Post::where('jenis','qna')
                        ->where('stat_post',0)
                        ->get());
        $jmlForum = count(Models\Post::where('jenis','forum')
                        ->where('stat_post',0)
                        ->get());
        
        $qna = Models\Post::where('stat_post',0)->where('jenis','qna')
        ->orderBy('jml_like','DESC')->limit(2)->get();

        $forum = Models\Post::where('stat_post',0)->where('jenis','forum')
        ->orderBy('jml_like','DESC')->limit(2)->get();

        $subs = Models\Subs::orderBy('tgl_subs','DESC')->limit(5)->get();
        $arr = [];

        // $post = Controllers\Post\PostController::getPost($qna);
        for($i=0;$i<count($qna);$i++){
            $arr1 = [];
            $idTheme = $qna[$i]->theme->id;
            $videoTheme = Models\VideoTheme::where('id_theme',$idTheme)->first();
            // if(!$videoTheme){
            //     continue;
            // }
            $classes = $videoTheme->video->content->classes->nama;
            $arr1 = [
                'nama' => $qna[$i]->user->nama,
                'deskripsi' => $qna[$i]->deskripsi,
                'class-nama' => $classes,
                'qna-uuid' => $qna[$i]->uuid
            ];
            $arr[$i] = $arr1;
        }

        $arr0 = [];

        // $post = Controllers\Post\PostController::getPost($qna);
        for($i=0;$i<count($forum);$i++){
            $arr01 = [];
            $arr01 = [
                'nama' => $forum[$i]->user->nama,
                'judul' => $forum[$i]->judul,
                'forum-uuid' => $forum[$i]->uuid
            ];
            $arr0[$i] = $arr01;
        }

        $arr2 = [];

        // $post = Controllers\Post\PostController::getPost($qna);
        for($i=0;$i<count($subs);$i++){
            $arr01 = [];
            $stat = "Aktif";
            if(date_format(date_create($subs[$i]->user->tgl_langganan_akhir),"Y/m/d") < date("Y/m/d")){
                $stat = "Non-Aktif";
            }
            $arr01 = [
                'nama' => $subs[$i]->user->nama,
                'email' => $subs[$i]->user->email,
                'paket-jenis' => $subs[$i]->packet->lama_paket,
                'paket-status' => $stat,
                'user-uuid' => $subs[$i]->user->uuid,
            ];
            $arr2[$i] = $arr01;
        }

        #$result['theme'] = $arr;

        $result = [
            'jml-siswa' => $jmlSubs,
            'jml-subs' => $jmlSiswa,
            'jml-mentor' => $jmlMentor,
            'jml-class' => $jmlClass,
            'jml-qna' => $jmlQnA,
            'jml-forum' => $jmlForum,
            'qna' => $arr,
            'forum' => $arr0,
            'subs' => $arr2,
        ];
        
        return response()->json([
            'message' => 'Success',
            'data'    => $result
        ]);                
    }

}
