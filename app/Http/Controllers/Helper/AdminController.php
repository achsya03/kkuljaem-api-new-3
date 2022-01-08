<?php

namespace App\Http\Controllers\Helper;

use App\Models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//use App\Http\Controllers\Auth;

use App\Http\Controllers\Banner;

use App\Http\Controllers\Classes;

use App\Http\Controllers\Helper;
use App\Http\Controllers\Payment;
use Illuminate\Support\Facades\Auth;

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

        //Auth::logoutOtherDevices(bcrypt($request->user()->password));

        $result = [];
        $jmlSiswa = count(Models\User::where('jenis_pengguna',0)->get());
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
            $bad_word = Models\BadWord::select('kata')->get();

            $arr_badWord = [];
            for($k=0;$k<count($bad_word);$k++){
                $arr_badWord[$k] = strtolower($bad_word[$k]->kata);
            }
            $judul = '';
            $juduls = explode(" ", $qna[$i]->judul);
            for($l=0;$l<count($juduls);$l++){
                if(in_array(strtolower($juduls[$l]),$arr_badWord)){
                    $len = strlen($juduls[$l]);
                    $juduls[$l] = '';
                    for($j=0;$j<$len;$j++){
                        $juduls[$l] = $juduls[$l].'*';
                    }
                }
                $judul = $judul.$juduls[$l];
                if($l<count($juduls)-1){
                    $judul = $judul.' ';
                }
            }

            $deskripsi = '';
            $deskripsis = explode(" ", $qna[$i]->deskripsi);
            for($l=0;$l<count($deskripsis);$l++){
                if(in_array(strtolower($deskripsis[$l]),$arr_badWord)){
                    $len = strlen($deskripsis[$l]);
                    $deskripsis[$l] = '';
                    for($j=0;$j<$len;$j++){
                        $deskripsis[$l] = $deskripsis[$l].'*';
                    }
                }
                $deskripsi = $deskripsi.$deskripsis[$l];
                if($l<count($deskripsis)-1){
                    $deskripsi = $deskripsi.' ';
                }
            }
            $arr1 = [
                'nama' => $qna[$i]->user->nama,
                'deskripsi' => $deskripsi,
                'class-nama' => $classes,
                'qna-uuid' => $qna[$i]->uuid
            ];
            $arr[$i] = $arr1;
        }

        $arr0 = [];

        // $post = Controllers\Post\PostController::getPost($qna);
        for($i=0;$i<count($forum);$i++){
            $arr01 = [];
            $bad_word = Models\BadWord::select('kata')->get();

            $arr_badWord = [];
            for($k=0;$k<count($bad_word);$k++){
                $arr_badWord[$k] = strtolower($bad_word[$k]->kata);
            }
            $judul = '';
            $juduls = explode(" ", $forum[$i]->judul);
            for($l=0;$l<count($juduls);$l++){
                if(in_array(strtolower($juduls[$l]),$arr_badWord)){
                    $len = strlen($juduls[$l]);
                    $juduls[$l] = '';
                    for($j=0;$j<$len;$j++){
                        $juduls[$l] = $juduls[$l].'*';
                    }
                }
                $judul = $judul.$juduls[$l];
                if($l<count($juduls)-1){
                    $judul = $judul.' ';
                }
            }

            $deskripsi = '';
            $deskripsis = explode(" ", $forum[$i]->deskripsi);
            for($l=0;$l<count($deskripsis);$l++){
                if(in_array(strtolower($deskripsis[$l]),$arr_badWord)){
                    $len = strlen($deskripsis[$l]);
                    $deskripsis[$l] = '';
                    for($j=0;$j<$len;$j++){
                        $deskripsis[$l] = $deskripsis[$l].'*';
                    }
                }
                $deskripsi = $deskripsi.$deskripsis[$l];
                if($l<count($deskripsis)-1){
                    $deskripsi = $deskripsi.' ';
                }
            }
            $arr01 = [
                'nama' => $forum[$i]->user->nama,
                'judul' => $judul,
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
            'jml-siswa' => $jmlSiswa,
            'jml-subs' => $jmlSubs,
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
