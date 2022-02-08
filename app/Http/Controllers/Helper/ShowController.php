<?php

namespace App\Http\Controllers\Helper;

use App\Models;
use App\Http\Controllers\Controller;
use App\Http\Controllers;
use App\Http\Controllers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShowController extends Controller
{
    #=========================Home===========================
    private function statUser($user){
        $stUsr = "Non-Member";
        $jenis_akun=['No Sign','Helm','Crown Silver'];
        if(date_format(date_create($user->tgl_langganan_akhir),"Y/m/d") >= date('Y/m/d')){
            $stUsr = "Member";
        }
        //$data['email'] = $user->email;
        if($user->jenis_pengguna!='0'){
            if(count($user->detailMentor)>0){
                if($user->detailMentor[0]->url_foto!=null || $user->detailMentor[0]->url_foto!=''){$data['foto'] = $user->detailMentor[0]->url_foto;}
            }
        }
        $data['tgl_akhir_langganan'] = $user->tgl_langganan_akhir;
        $data['nama'] = $user->nama;
        $data['status_member'] = $stUsr;
        $det_student = Models\DetailStudent::where('id_users',$user->id)->get();

        if(count($det_student)>0){
            $data['jenis_kelamin'] = $det_student[0]->jenis_kel;

        }
        //$data['jenis_akun'] = $jenis_akun[$user->jenis_akun];

        return $data;
    }

    public function __construct(Request $request){
        $this->middleware('auth');
    }

    public function home(Request $request){
        $result = [];

        //Auth::logoutOtherDevices(bcrypt($request->user()->password));

        $tglSekarang = date('Y/m/d');

        $banner = Models\Banner::orderBy('urutan','ASC')->get();
        $videos = Models\Videos::where('jadwal',$tglSekarang)->limit(1)->get();
        if(count($videos)==0){
            $videos = Models\Videos::orderBy('jadwal','ASC')->limit(1)->get();
        }
        $words = Models\Words::where('jadwal',$tglSekarang)->get();
        if(count($words)==0){
            $words = Models\Words::orderBy('jadwal','ASC')->limit(5)->get();
        }
        $class = Models\Classes::orderBy('urutan','ASC')
            ->where('status_tersedia',1)->limit(6)->get();
        $video_uuid = Models\Video::select('uuid')->get();
        $theme = Models\Theme::orderBy('urutan','ASC')
                ->whereNotIn('judul',$video_uuid)->get();
        $post = [];
        if(count($post = Models\Post::where('stat_post',0)->where('jenis','forum')
        ->where('stat_terpilih','1')->get())==0){
            $post = Models\Post::where('stat_post',0)->where('jenis','forum')
                ->orderBy('jml_like','DESC')->limit(10)->get();
        }

        $ban = [];
        for($i = 0;$i < count($banner); $i++){
            $ban[$i] = [
                'judul_banner' => $banner[$i]->judul_banner,
                'url_web' => $banner[$i]->url_web,
                'url_mobile' => $banner[$i]->url_mobile,
                'deskripsi' => $banner[$i]->deskripsi,
                'label' => $banner[$i]->label,
                'link' => $banner[$i]->link,
                'banner_uuid' => $banner[$i]->uuid
            ];
        }

        $token = bin2hex(random_bytes(32));
        // Session::put($token, $token);
        // Session::save();
        // Models\VideoSession::create([
        //     'key'                     => $token,
        //     'value'                   => $token,
        // ]);

        $vid = [];
        for($i = 0;$i < count($videos); $i++){
            $vid[$i] = [
                'url_video' => env('APP_DOMAIN').'videos/redirect?v='.substr($videos[$i]->url_video,32),
                //'url_video_web' => $videos[$i]->url_video_web,
                'url_video_mobile' => $videos[$i]->url_video,
                'video_uuid' => $videos[$i]->uuid
            ];
        }

        $wor = [];
        for($i = 0;$i < count($words); $i++){
            $wor[$i] = [
                'hangeul' => $words[$i]->hangeul,
                'pelafalan' => $words[$i]->pelafalan,
                'penjelasan' => $words[$i]->penjelasan,
                'url_pengucapan' => $words[$i]->url_pengucapan,
                'kata_uuid' => $words[$i]->uuid
            ];
        }

        $cls = [];
        for($i = 0;$i < count($class); $i++){
            $cl = Models\Teacher::where('id_class',$class[$i]->id)->first();
            
            
            $cls[$i]['nama_kelas'] = $class[$i]->nama;
            $cls[$i]['urutan_kelas'] = $class[$i]->urutan;
            if($cl != null){
                $cls[$i]['nama_mentor'] = $cl->user->nama;
            }
            $cls[$i]['url_web'] = $class[$i]->url_web;
            $cls[$i]['url_mobile'] = $class[$i]->url_mobile;
            $cls[$i]['jml_materi'] = $class[$i]->jml_video+$class[$i]->jml_kuis;
            $cls[$i]['kelas_uuid'] = $class[$i]->uuid;
        }

        $th = [];
        for($i = 0;$i < count($theme); $i++){
            $th[$i] = [
                'topik' => $theme[$i]->judul,
                'topik_image' => $theme[$i]->url_image,
                'topik_uuid' => $theme[$i]->uuid
            ];
        }

        $pos = Controllers\Post\PostController::getPost($post,$request->user()->id);

        $result['banner'] = $ban;
        $result['video'] = $vid;
        $result['word'] = $wor;
        $result['class'] = $cls;
        $result['theme'] = $th;
        $result['post'] = $pos;

        return response()->json([
            'message' => 'Success',
            'account' => $this->statUser($request->user()),
            'data'    => $result
        ]);
    }
    
    public function banner(Request $request){
        $banner = Controllers\Banner\BannerController::detailData($request->token,$this->statUser($request->user()));
        return $banner;
    }

    public function word(Request $request){
        $word = Controllers\Banner\WordController::detailDataWord($request->token,$this->statUser($request->user()));
        return $word;
    }

    public function video(Request $request){
        $video = Controllers\Banner\VideoController::detailDataVideo($request->token,$this->statUser($request->user()));
        return $video;
    }

    public function search(Request $request){
        // if(!$key=$request->keyword){
        //     return response()->json(['message' => 'Failed',
        //     'info'=>"Keyword Tidak Sesuai"]);
        // }
        $result  = [];

        $post = Models\Post::where('stat_post',0)
        ->where('jenis','forum')
        ->where('judul','LIKE','%'.strtolower($request->keyword).'%')
        //->where('judul','ilike','%'.$request->keyword.'%')
        ->get();

        $qna = Models\Post::where('stat_post',0)
        ->where('jenis','qna')
        ->where('judul','LIKE','%'.strtolower($request->keyword).'%')
        //->where('judul','ilike','%'.$request->keyword.'%')
        ->get();

        $pos = Controllers\Post\PostController::getPost($post,$request->user()->id);
        $qn = Controllers\Post\PostController::getPost($qna,$request->user()->id);

        $result = [
            'jml_forum' => count($pos),
            'forum' => $pos,
            'jml_qna' => count($qn),
            'qna' => $qn
        ];

        return response()->json([
            'message' => 'Success',
            'account' => $this->statUser($request->user()),
            'data'    => $result
        ]);
    }

    #=========================Home===========================
    #=========================Classroom===========================

    private function userCheck($uuid,$date){
        $stUsr = "Non-Member";
        if($date >= date('Y/m/d')){
            $stUsr = "Member";
        }

        return $stUsr;
    }

    public function classroom(Request $request){
        $result = [];
                
        $usr = Models\User::where('uuid',$request->user()->uuid)->first();
        $date = date_format(date_create($usr->tgl_langganan_akhir),"Y/m/d");
       
        //$result['stat_pengguna'] = $this->userCheck($request->user()->uuid,$date);

        $category = Models\ClassesCategory::orderBy('urutan','ASC')->get();
        $arr0 = [];
        for($i = 0;$i < count($category);$i++){
            $arr = [];
            $class = Models\Classes::where('id_class_category',$category[$i]->id)
                ->where('status_tersedia',1)->orderBy('urutan','ASC')->limit(6)->get();
            $classes = [];
            for($j = 0;$j < count($class);$j++){
                $arr1 = [];
                $arr1['class_nama'] = $class[$j]->nama;
                $arr1['class_urutan'] = $class[$j]->urutan;
                $arr1['url_web'] = $class[$j]->url_web;
                $arr1['url_mobile'] = $class[$j]->url_mobile;
                $arr1['jml_materi'] = $class[0]->jml_video+$class[0]->jml_kuis;
                $arr1['class_uuid'] = $class[$j]->uuid;
                $teacher = Models\Teacher::where('id_class',$class[$j]->id)->get();
                // for($k=0;$k<count($teacher);$k++){
                    // $arr1['mentor'][$k]['mentor_nama'] = $teacher[$k]->user->nama;
                    // $arr1['mentor'][$k]['mentor_uuid'] = $teacher[$k]->user->uuid;
                // }
                $arr1['mentor']['mentor_nama'] = $teacher[0]->user->nama;
                $arr1['mentor']['mentor_uuid'] = $teacher[0]->user->uuid;
                $classes[$j] = $arr1;
            }


            $arr['category'] = $category[$i]->nama;
            $arr['category_detail'] = $category[$i]->deskripsi;
            $arr['category_uuid'] = $category[$i]->uuid;
            $arr['classroom'] = $classes;
            $arr0[$i] = $arr;
        }
        $result['class_list'] = $arr0;
        #$result['class_terdaftar'] = $this->classroomRegistered($uuidUser);

        return response()->json([
            'message' => 'Success',
            'account' => $this->statUser($request->user()),
            'data'    => $result
        ]);
    }

    public function classroomByCategory(Request $request){
        $result = [];
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        
        $category = Models\ClassesCategory::where('uuid',$uuid)->get();
        if(count($category)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        $arr = [];
        $class = Models\Classes::where('id_class_category',$category[0]->id)
            ->where('status_tersedia',1)->orderBy('urutan','ASC')->get();
            
        $classes = [];
        $arr0 = [];
        for($j = 0;$j < count($class);$j++){
            $arr1 = [];
            $arr1['class_nama'] = $class[$j]->nama;
            $arr1['class_urutan'] = $class[$j]->urutan;
            $arr1['url_web'] = $class[$j]->url_web;
            $arr1['url_mobile'] = $class[$j]->url_mobile;
            $arr1['jml_materi'] = $class[$j]->jml_video+$class[$j]->jml_kuis;
            $arr1['class_uuid'] = $class[$j]->uuid;
            $teacher = Models\Teacher::where('id_class',$class[$j]->id)->first();
            if($teacher != null){
                $tcr = Models\Teacher::find($teacher->id);
                $usr = Models\User::find($teacher->id_user);
                $arr1['mentor_nama'] = $tcr->user->nama;
                #$arr['mentor-foto'] = $usr->detailMentor[0]->url_foto;
                $arr1['mentor_uuid'] = $tcr->uuid;
            }
            $classes[$j] = $arr1;
        }    

        $arr['category'] = $category[0]->nama;
        $arr['deskripsi'] = $category[0]->deskripsi;
        $arr['category_uuid'] = $category[0]->uuid;
        $arr ['class']= $classes;

        $result = $arr;

        return response()->json([
            'message' => 'Success',
            'account' => $this->statUser($request->user()),
            'data'    => $result
        ]);
    }

    //Masih Plain belum ada validasi member
    public function classroomDetail(Request $request){
        $result = [];
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        $usr = Models\User::where('uuid',$request->user()->uuid)->first();
        $date = date_format(date_create($usr->tgl_langganan_akhir),"Y/m/d");
       

        $classes = Models\Classes::where('uuid',$uuid)->get();
        
        if(count($classes)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        $arr = [];
        $cont = [];
        $content = Models\Content::where('id_class',$classes[0]->id)->orderBy('type', 'DESC')->orderBy('number', 'ASC')->get();
        $content_id = Models\Content::where('id_class',$classes[0]->id)->orderBy('type', 'DESC')->orderBy('number', 'ASC')->get();
        $count_vid = 0;
        $count_quiz = 0;
        //$arr['stat_pengguna'] = $this->userCheck($uuidUser,$date);

        //return $content;
        for($i = 0;$i < count($content);$i++){
            $arr1 = [];
            if($content[$i]->type == 'video'){
                $count_vid += 1;
                $content_video = Models\Video::where('id_content',$content[$i]->id)->get();
                $arr1['urutan'] = $content[$i]->number;
                $arr1['judul'] = $content_video[0]->judul;
                $arr1['type'] = $content[$i]->type;
                $arr1['jml_latihan'] = $content_video[0]->jml_latihan;
                $arr1['jml_shadowing'] = $content_video[0]->jml_shadowing;

                $stat = 'Belum';
                if(count($studentVideo = Models\StudentVideo::where('id_video',$content_video[0]->id)->get())!=0){
                    for($j = 0;$j<count($studentVideo);$j++){
                        if($studentVideo[$j]->student->id_user == $request->user()->id){
                            $stat = 'Selesai';
                            break;
                        }
                    }
                }

                $arr1['stat_pengerjaan'] = $stat;
                $arr1['content_video_uuid'] = $content_video[0]->uuid;
            }elseif($content[$i]->type == 'quiz'){
                $count_quiz += 1;
                $content_quiz = Models\Quiz::where('id_content',$content[$i]->id)->get();
                $arr1['urutan'] = $content[$i]->number;
                $arr1['judul'] = $content_quiz[0]->judul;
                $arr1['type'] = $content[$i]->type;
                $arr1['jml_soal'] = $content_quiz[0]->jml_pertanyaan;

                $stat = 'Belum';
                if(count($studentQuiz = Models\StudentQuiz::where('id_quiz',$content_quiz[0]->id)->get())!=0){
                    for($j = 0;$j<count($studentQuiz);$j++){
                        if($studentQuiz[$j]->student->id_user == $request->user()->id){
                            $stat = 'Selesai';
                            break;
                        }
                    }
                }

                $arr1['stat_pengerjaan'] = $stat;
                $arr1['content_quiz_uuid'] = $content_quiz[0]->uuid;
            }
            $cont[$i] = $arr1;
        }
        $stat = ['Tidak Tersedia','Tersedia'];

        $arr['class_nama'] = $classes[0]->nama;
        $arr['class_desc'] = $classes[0]->deskripsi;
        $arr['class_desc'] = $classes[0]->deskripsi;
        $arr['class_tersedia'] = $stat[$classes[0]->status_tersedia];
        $arr['url_web'] = $classes[0]->url_web;
        $arr['url_mobile'] = $classes[0]->url_mobile;
        $arr['class_uuid'] = $classes[0]->uuid;
        $arr['jml_video'] = $count_vid;
        $arr['jml_quiz'] = $count_quiz;

        $teacher = Models\Teacher::where('id_class',$classes[0]->id)->first();
        if($teacher != null){
            $tcr = Models\Teacher::find($teacher->id);
            $usr = Models\User::find($teacher->id_user);
            $arr['mentor_nama'] = $tcr->user->nama;
            if(count($usr->detailMentor)>0){
                if(isset($usr->detailMentor[0]->url_foto)){
                    $arr['mentor_foto'] = $usr->detailMentor[0]->url_foto;
                }
            }
            $arr['mentor_uuid'] = $tcr->uuid;
        }
        $arr['content'] = $cont;

        $result = $arr;

        return response()->json([
            'message' => 'Success',
            'account' => $this->statUser($request->user()),
            'data'    => $result
        ]);
    }

    public function classroomMentorDetail(Request $request){
        $result = [];
        #uuid mentor
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        $uuidUser = $request->user_uuid;

        $teacher = Models\Teacher::where('uuid',$uuid)->get();
        
        if(count($teacher)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        //$teacher = Models\Teacher::where('id_user',$user[0]->id)->get();
        $usr = Models\User::where('id',$teacher[0]->id_user)->first();
        $teacher = Models\Teacher::where('id_user',$usr->id)->get();
        
        //return $usr->detailMentor;
        $arr['mentor_nama'] = $usr->nama;
        $arr['mentor_lama'] = date_format(date_create($usr->created_at),"Y");
        if(count($usr->detailMentor)>0){
            $arr['mentor_bio'] = $usr->detailMentor[0]->bio;
            if($usr->detailMentor[0]->url_foto!=null){
                $arr['mentor_foto'] = $usr->detailMentor[0]->url_foto;
            }
        }
        $arr['mentor_uuid'] = $uuid;
        $cls = [];$co=0;
        //$tc = Models\Teacher::where('id_user',$teacher[0]->id_user)->get();
        for($i=0;$i<count($teacher);$i++){
            if(count($classes = Models\Classes::where('id',$teacher[$i]->id_class)
                ->where('status_tersedia',1)->orderBy('nama','ASC')->get())==0){
                    continue;
                }
            $arr0 = [];
            $arr0 = [
                    'class_nama' => $classes[0]->nama,
                    'class_url_web' => $classes[0]->url_web,
                    'class_url_mobile' => $classes[0]->url_mobile,
                    'class_jml_materi' => $classes[0]->jml_video+$classes[0]->jml_kuis,
                    'class_uuid' => $classes[0]->uuid
                ];
            $cls[$co] = $arr0;
            $co++;
        }
        $arr['classroom'] = $cls;

        $result = $arr;

        return response()->json([
            'message' => 'Success',
            'account' => $this->statUser($request->user()),
            'data'    => $result
        ]);
    }

    public function classroomRegistered(Request $request){
        $result = [];

        $uuid = $request->user()->uuid;

        $usr = Models\User::find($request->user()->id);

        $classes = $usr->student;
        $arr = [];
        $reg_id = [];
        for($i=0;$i<count($classes);$i++){
            $arr0 = [];
            $class = Models\Classes::find($classes[$i]->id_class);
            $usr['nama'] = [];
            if(count($class->teacher)>0){
                $usr = Models\User::where('id',$class->teacher[0]->id_user)->first();
            }
            $arr0['class_nama'] = $class->nama;
            $arr0['class_url_web'] = $class->url_web;
            $arr0['class_url_mobile'] = $class->url_mobile;
            if($usr->nama != null){
                $arr0['mentor_nama'] = $usr->nama;
            }
            $arr0['class_jml_materi'] = $class->jml_video+$class->jml_kuis;
            $arr0['class_tersedia'] = $class->status_tersedia;
            $total = ($class->jml_video+$class->jml_kuis);
            if($total==0){
                $total = 1;
            }
            $arr0['class_prosentase'] = ($classes[$i]->jml_pengerjaan / $total) * 100;
            $arr0['class_uuid'] = $class->uuid;
            $arr[$i] = $arr0;
            $reg_id[$i] = $classes[$i]->id_class;
        }
        $result['class_terdaftar'] = $arr;

        $arr = [];
        $class = Models\Classes::whereNotIn('id',$reg_id)
            ->where('status_tersedia',1)->get();
        for($i=0;$i<count($class);$i++){
            $arr0 = [];
            $tcr = Models\Teacher::where('id_class',$class[$i]->id)->first();
            $usr['nama'] = [];
            if($tcr != null){
                $usr = Models\User::where('id',$tcr->id_user)->first();
            }
            // $arr0 = [
            //     'class_nama' => $class[$i]->nama,
            //     'class_url_web' => $class[$i]->url_web,
            //     'class_url_mobile' => $class[$i]->url_mobile,
            //     'mentor_nama' => $usr->nama,
            //     'class_jml_materi' => $class[$i]->jml_video+$class[$i]->jml_kuis,
            //     #'class_prosentase' => ($classes[$i]->jml_pengerjaan / $class->jml_materi) * 100,
            //     'class_uuid' => $class[$i]->uuid
            // ];
            $arr0['class_nama'] = $class[$i]->nama;
            $arr0['class_url_web'] = $class[$i]->url_web;
            $arr0['class_url_mobile'] = $class[$i]->url_mobile;
            if($usr->nama != null){
                $arr0['mentor_nama'] = $usr->nama;
            }
            $arr0['class_jml_materi'] = $class[$i]->jml_video+$class[$i]->jml_kuis;
            //$arr0['class_tersedia'] = $class->status_tersedia;
            //$arr0['class_prosentase'] = ($class->jml_pengerjaan / ($class->jml_video+$class->jml_kuis)) * 100;
            $arr0['class_uuid'] = $class[$i]->uuid;
            $arr[$i] = $arr0;
        }
        $result['class_tidak_terdaftar'] = $arr;

        return response()->json([
            'message' => 'Success',
            'account' => $this->statUser($request->user()),
            'data'    => $result
        ]);
    }
    #=========================Classroom===========================

    #=========================Classroom-Content===========================
    public function classroomVideoDetail(Request $request){
        #video token
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        if(count($video = Models\Video::where('uuid',$uuid)->get())==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        $result = [];
        $token = bin2hex(random_bytes(32));
        // Session::put($token, $token);
        // Session::save();
        // Models\VideoSession::create([
        //     'key'                     => $token,
        //     'value'                   => $token,
        // ]);

        $arr = [
            'judul' => $video[0]->judul,
            'keterangan' => $video[0]->keterangan,
            'url_video' => env('APP_DOMAIN').'video/redirect?v='.substr($video[0]->url_video,32),
            //'url_video_web' => $video[0]->url_video_web,
            'url_video_mobile' => $video[0]->url_video,
            'video_uuid' => $video[0]->uuid,
        ];

        $content = Models\Content::where('id_class',$video[0]->content->id_class)->get();
        #return $content;
        $count_vid=0;
        $count_quiz=0;
        $cont = [];
        for($i = 0;$i < count($content);$i++){
            $arr1 = [];
            if($content[$i]->type == 'video'){
                $count_vid += 1;
                $content_video = Models\Video::where('id_content',$content[$i]->id)->get();
                $arr1['urutan'] = $content[$i]->number;
                $arr1['judul'] = $content_video[0]->judul;
                $arr1['type'] = $content[$i]->type;
                //$arr1['jml_latihan'] = $content_video[0]->jml_latihan;
                $arr1['jml_latihan'] = count(Models\Task::where('id_video',$content_video[0]->id)->get());
                //$arr1['jml_shadowing'] = $content_video[0]->jml_shadowing;
                $arr1['jml_shadowing'] = count(Models\Shadowing::where('id_video',$content_video[0]->id)->get());

                $stat = 'Belum';
                if(count($studentVideo = Models\StudentVideo::where('id_video',$content_video[0]->id)->get())!=0){
                    for($j = 0;$j<count($studentVideo);$j++){
                        if($studentVideo[$j]->student->id_user == $request->user()->id){
                            $stat = 'Selesai';
                            break;
                        }
                    }
                }

                $arr1['stat_pengerjaan'] = $stat;
                $arr1['content_video_uuid'] = $content_video[0]->uuid;
            }elseif($content[$i]->type == 'quiz'){
                $count_quiz += 1;
                $content_quiz = Models\Quiz::where('id_content',$content[$i]->id)->get();
                $arr1['urutan'] = $content[$i]->number;
                $arr1['judul'] = $content_quiz[0]->judul;
                $arr1['type'] = $content[$i]->type;
                $arr1['jml_soal'] = $content_quiz[0]->jml_pertanyaan;

                $stat = 'Belum';
                if(count($studentQuiz = Models\StudentQuiz::where('id_quiz',$content_quiz[0]->id)->get())!=0){
                    for($j = 0;$j<count($studentQuiz);$j++){
                        if($studentQuiz[$j]->student->id_user == $request->user()->id){
                            $stat = 'Selesai';
                            break;
                        }
                    }
                }

                $arr1['stat_pengerjaan'] = $stat;
                $arr1['content_quiz_uuid'] = $content_quiz[0]->uuid;
            }
            $cont[$i] = $arr1;
        }

        $arr['content'] = $cont;
        $result = $arr;

        return response()->json([
            'message' => 'Success',
            'account' => $this->statUser($request->user()),
            'data'    => $result
        ]);
    }

    public function classroomQuizDetail(Request $request){
        #video token
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        if(count($quiz = Models\Quiz::where('uuid',$uuid)->get())==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        $result = [];
        //return $quiz[0]->id;
        $exam = Models\Exam::where('id_quiz',$quiz[0]->id)
        ->orderBy('number','ASC')->get();

        
        $arr = [];
        for($i = 0;$i < count($exam);$i++){
            $arr1 = [];
            $question = Models\Question::where('id',$exam[$i]->id_question)
                ->get();
            $option = Models\Option::where('id_question',$question[0]->id)->orderBy('id','ASC')
                ->get();


            if($quiz[0]->judul != null){$arr1['judul_quiz'] = $quiz[0]->judul;}
            if($question[0]->pertanyaan_teks != null){$arr1['pertanyaan_teks'] = $question[0]->pertanyaan_teks;}
            if($question[0]->url_gambar != null){$arr1['url_gambar'] = $question[0]->url_gambar;}
            if($question[0]->url_file != null){$arr1['url_file'] = $question[0]->url_file;}
            $arr1['jawaban'] = $question[0]->jawaban;
            $arr1['jenis_jawaban'] = $question[0]->jenis_jawaban;
            $arr1['question_uuid'] = $question[0]->uuid;
            $arr0 = [];

            for($j = 0;$j < count($option);$j++){
                $arr2 = [];
                $opt = ['A','B','C','D'];
                if($option[$j]->jawaban_teks != null){$arr2['jawaban_teks'] = $option[$j]->jawaban_teks;}
                if($option[$j]->url_gambar != null){$arr2['url_gambar'] = $option[$j]->url_gambar;}
                if($option[$j]->url_file != null){$arr2['url_file'] = $option[$j]->url_file;}
                $arr2['jawaban_id'] = $opt[$j];
                $arr2['option_uuid'] = $option[$j]->uuid;
                $arr0[$j] = $arr2;
            }
            $arr1['option'] = $arr0;
            $arr[$i] = $arr1;
        }

        //$arr['content'] = $cont;
        $result = $arr;

        return response()->json([
            'message' => 'Success',
            'account' => $this->statUser($request->user()),
            'data'    => $result
        ]);
    }
    #=========================Classroom-Video-More===========================
    public function classroomVideoMore(Request $request){
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        if(count($quiz = Models\Video::where('uuid',$uuid)->get())==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        $result = [];

        $task = Models\Task::where('id_video',$quiz[0]->id)->get();
        $shadowing = Models\Shadowing::where('id_video',$quiz[0]->id)->get();

        $result['jml_latihan'] = count($task);
        $result['jml_shadowing'] = count($shadowing);
        $result['video_uuid'] = $uuid;

        return response()->json([
            'message' => 'Success',
            'account' => $this->statUser($request->user()),
            'data'    => $result
        ]);
    }

    public function classroomVideoTask(Request $request){
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        if(count($quiz = Models\Video::where('uuid',$uuid)->get())==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        $result = [];

        $task = Models\Task::where('id_video',$quiz[0]->id)->get();

        $arr = [];
        for($i = 0;$i < count($task);$i++){
            $arr1 = [];
            $question = Models\Question::where('id',$task[$i]->id_question)
                ->get();
            $option = Models\Option::where('id_question',$question[0]->id)->orderBy('id','ASC')
                ->get();

            $arr1['nomor'] = $question[0]->task[0]->number;
            if($question[0]->pertanyaan_teks != null){$arr1['pertanyaan_teks'] = $question[0]->pertanyaan_teks;}
            if($question[0]->url_gambar != null){$arr1['url_gambar'] = $question[0]->url_gambar;}
            if($question[0]->url_file != null){$arr1['url_file'] = $question[0]->url_file;}
            $arr1['jawaban'] = $question[0]->jawaban;
            $arr1['jenis_jawaban'] = $question[0]->jenis_jawaban;
            $arr1['question_uuid'] = $question[0]->uuid;
            $arr0 = [];

            for($j = 0;$j < count($option);$j++){
                $arr2 = [];
                $opt = ['A','B','C','D'];
                if($option[$j]->jawaban_teks != null){$arr2['jawaban_teks'] = $option[$j]->jawaban_teks;}
                if($option[$j]->url_gambar != null){$arr2['url_gambar'] = $option[$j]->url_gambar;}
                if($option[$j]->url_file != null){$arr2['url_file'] = $option[$j]->url_file;}
                $arr2['jawaban_id'] = $opt[$j];
                $arr2['option_uuid'] = $option[$j]->uuid;
                $arr0[$j] = $arr2;
            }
            $arr1['option'] = $arr0;
            $arr[$i] = $arr1;
        }

        $result = $arr;

        return response()->json([
            'message' => 'Success',
            'account' => $this->statUser($request->user()),
            'data'    => $result
        ]);
    }

    public function classroomVideoShadowing(Request $request){
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        if(count($quiz = Models\Video::where('uuid',$uuid)->get())==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        $result = [];

        $shadowing = Models\Shadowing::where('id_video',$quiz[0]->id)->get();
        $arr = [];
        for($i=0;$i<count($shadowing);$i++){
            $arr1 = [];
            $word = Models\Words::where('id',$shadowing[$i]->id_word)->first();
            $arr1['hangeul'] =  $word->hangeul;
            $arr1['pelafalan'] =  $word->pelafalan;
            $arr1['penjelasan'] =  $word->penjelasan;
            $arr1['url_pengucapan'] =  $word->url_pengucapan;
            $arr1['uuid'] =  $word->uuid;
            $arr[$i] = $arr1;
        }

        $result = $arr;

        return response()->json([
            'message' => 'Success',
            'account' => $this->statUser($request->user()),
            'data'    => $result
        ]);
    }
    #=========================Classroom-Video-More===========================
    #=========================Classroom-Content===========================

    #=========================QnA===========================
    public function qna(Request $request){
        $result = [];
        #$forum = Models\Post::where('jenis','forum')->where('stat_post','0')->get();
        
        #$theme = Models\Theme::orderBy('jml_post','DESC')->limit(3)->get();

        $post = Models\Post::where('stat_post',0)->where('jenis','qna')
        ->orderBy('created_at','DESC')->get();

       
        
        $arr = [];
        $arr01 = [];
        $paginate = 2;
        $total_page = 0;
        $page_counter = 0;
        $arr010 = [];

        // $post = Controllers\Post\PostController::getPost($qna);
        for($i=0;$i<count($post);$i++){
            $arr1 = [];
            $idTheme = $post[$i]->theme->id;
            $videoTheme = Models\VideoTheme::where('id_theme',$idTheme)->first();
            $video = $videoTheme->video;

            $posting = 'False';
            $like = 'False';
            $alert = 'False';
            $userId = $request->user()->id;

            if($post[$i]->id_user==$userId){
                $posting = 'True';
            }
            if(count($likes = Models\PostLike::where('id_post',$post[$i]->id)
                            ->where('id_user',$userId)->get())>0){
                $like = 'True';
            }
            if(count($alerts = Models\PostAlert::where('id_post',$post[$i]->id)
                            ->where('id_user',$userId)
                            ->where('alert_status',0)->get())>0 || $posting == 'True'){
                $alert = 'True';
            }
            $arr0 = [];
            $st_user = ["new user","data complete","member data complete","admin-mentor"];
            $st_user_post = $st_user[0];
            if($post[$i]->user->jenis_pengguna=='0') {
                if($post[$i]->user->jenis_akun=='0'){
                    $st_user_post = $st_user[0];
                }elseif($post[$i]->user->jenis_akun==2){
                    $st_user_post = $st_user[1];
                }
            }
            if(date_format(date_create($post[$i]->user->tgl_langganan_akhir),"Y/m/d") >= date('Y/m/d')) {
                $st_user_post = $st_user[2];
            }
            if($post[$i]->user->jenis_pengguna==1 || $post[$i]->user->jenis_pengguna==2) {
                $st_user_post = $st_user[3];
            }

            $bad_word = Models\BadWord::select('kata')->get();

            $arr_badWord = [];
            for($k=0;$k<count($bad_word);$k++){
                $arr_badWord[$k] = strtolower($bad_word[$k]->kata);
            }
            $judul = '';
            $juduls = explode(" ", $post[$i]->judul);
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
            $deskripsis = explode(" ", $post[$i]->deskripsi);
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
                'judul' => $judul,
                'user_posting' => $posting,
                'user_like' => $like,
                'user_lapor' => $alert,
                'deskripsi' => $deskripsi,
                'nama_pengirim' => $post[$i]->user->nama,
                'stat_pengirim' => $st_user_post,
                //'stat_pengirim' => $st_user_post,
            ];
            $det_student = Models\DetailStudent::where('id_users',$post[$i]->id_user)->get();
            if(count($det_student)>0){
                $arr1['jenis_kelamin'] = $det_student[0]->jenis_kel;

            }
            $usr_id = $post[$i]->user->id;
            $detail_mentor = Models\DetailMentor::where('id_users',$usr_id)->get();
            if(count($detail_mentor)>0){
                if($detail_mentor[0]->url_foto != null && $post[$i]->user->jenis_pengguna != '0'){
                    $arr1 += [
                        'foto_pengirim' => $detail_mentor[0]->url_foto,
                    ];
                }
            }
            $arr1 += [
                'tgl_post' => $post[$i]->created_at,
                'jml_like' => $post[$i]->jml_like,
                'jml_komen' => $post[$i]->jml_komen,
                'video_judul' => $video->judul,
                'video_uuid' => $video->uuid,
                'post_uuid' => $post[$i]->uuid
            ];
            $arr010[$page_counter] = $arr1;
            $page_counter += 1;
            if($page_counter % $paginate == 0){
                $arr01[$total_page] = $arr010;
                $arr010 = [];
                $page_counter = 0;
                $total_page += 1;
            }
            $arr[$i] = $arr1;
        }

        #$result['theme'] = $arr;
        $result = $arr;
        //$result['test_pagination'] = $arr01;

        return response()->json([
            'message' => 'Success',
            'account' => $this->statUser($request->user()),
            'data'    => $result,
            //'data1'    => $arr01
        ]);
    }

    public function qnaByVideo(Request $request){
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        if(count($video = Models\Video::where('uuid',$uuid)->get())==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        $result = [];

        $arr = [];
        $arr00 = [];

        #check
        if(count($idTheme = $video[0]->videoTheme)==0){

            return response()->json([
                'message' => 'Success',
                'account' => $this->statUser($request->user()),
                'data'    => $result
            ]);
        }
        $idTheme = $video[0]->videoTheme[0]->id_theme;
        $post = Models\Post::where('id_theme',$idTheme)->where('stat_post',0)->where('jenis','qna')
        ->orderBy('created_at','DESC')->get();

        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 10;
        $counter_forum = count($post);
        $max_page = ceil($counter_forum / $limit);

        $forum1 = Models\Post::where('id_theme',$idTheme)->where('stat_post',0)->where('jenis','qna')
                ->orderBy('created_at','DESC')
                ->limit($limit)->offset(($page - 1) * $limit)->get();
                
        for($i=0;$i<count($post);$i++){
            $arr1 = [];
            $posting = 'False';
            $like = 'False';
            $alert = 'False';
            $userId = $request->user()->id;

            if($post[$i]->id_user==$userId){
                $posting = 'True';
            }
            if(count($likes = Models\PostLike::where('id_post',$post[$i]->id)
                            ->where('id_user',$userId)->get())>0){
                $like = 'True';
            }
            if(count($alerts = Models\PostAlert::where('id_post',$post[$i]->id)
                            ->where('id_user',$userId)
                            ->where('alert_status',0)->get())>0 || $posting == 'True'){
                $alert = 'True';
            }
            $arr0 = [];

            $st_user = ["new user","data complete","member data complete","admin-mentor"];

            $st_user_post = $st_user[0];
            if($post[$i]->user->jenis_pengguna=='0') {
                if($post[$i]->user->jenis_akun=='0'){
                    $st_user_post = $st_user[0];
                }elseif($post[$i]->user->jenis_akun==2){
                    $st_user_post = $st_user[1];
                }
            }
            if(date_format(date_create($post[$i]->user->tgl_langganan_akhir),"Y/m/d") >= date('Y/m/d')) {
                $st_user_post = $st_user[2];
            }
            if($post[$i]->user->jenis_pengguna==1 || $post[$i]->user->jenis_pengguna==2) {
                $st_user_post = $st_user[3];
            }           

            $bad_word = Models\BadWord::select('kata')->get();

            $arr_badWord = [];
            for($k=0;$k<count($bad_word);$k++){
                $arr_badWord[$k] = strtolower($bad_word[$k]->kata);
            }
            $judul = '';
            $juduls = explode(" ", $post[$i]->judul);
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
            $deskripsis = explode(" ", $post[$i]->deskripsi);
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
                'judul' => $judul,
                'user_posting' => $posting,
                'user_like' => $like,
                'user_lapor' => $alert,
                'deskripsi' => $deskripsi,
                'nama_pengirim' => $post[$i]->user->nama,
                'stat_pengirim' => $st_user_post,
            ];
            $det_student = Models\DetailStudent::where('id_users',$post[$i]->id_user)->get();
            if(count($det_student)>0){
                $arr1['jenis_kelamin'] = $det_student[0]->jenis_kel;

            }
            $usr_id = $post[$i]->user->id;
            $detail_mentor = Models\DetailMentor::where('id_users',$usr_id)->get();
            if(count($detail_mentor)>0){
                if($detail_mentor[0]->url_foto != null && $post[$i]->user->jenis_pengguna != '0'){
                    $arr1 += [
                        'foto_pengirim' => $detail_mentor[0]->url_foto,
                    ];
                }
            }
            $arr1 += [
                'tgl_post' => $post[$i]->created_at,
                'jml_like' => $post[$i]->jml_like,
                'jml_komen' => $post[$i]->jml_komen,
                'video_number' => $video[0]->content->number,
                'video_judul' => $video[0]->judul,
                'video_uuid' => $video[0]->uuid,
                'post_uuid' => $post[$i]->uuid
            ];
            $arr[$i] = $arr1;
        }
        for($i=0;$i<count($forum1);$i++){
            $arr1 = [];
            $posting = 'False';
            $like = 'False';
            $alert = 'False';
            $userId = $request->user()->id;

            if($forum1[$i]->id_user==$userId){
                $posting = 'True';
            }
            if(count($likes = Models\PostLike::where('id_post',$forum1[$i]->id)
                            ->where('id_user',$userId)->get())>0){
                $like = 'True';
            }
            if(count($alerts = Models\PostAlert::where('id_post',$forum1[$i]->id)
                            ->where('id_user',$userId)
                            ->where('alert_status',0)->get())>0 || $posting == 'True'){
                $alert = 'True';
            }
            $arr0 = [];
            
            $arr1 = [
                'judul' => $forum1[$i]->judul,
                'user_posting' => $posting,
                'user_like' => $like,
                'user_lapor' => $alert,
                'deskripsi' => $forum1[$i]->deskripsi,
                'nama_pengirim' => $forum1[$i]->user->nama
            ];

            $det_student = Models\DetailStudent::where('id_users',$forum1[$i]->id_user)->get();
            if(count($det_student)>0){
                $arr1['jenis_kelamin'] = $det_student[0]->jenis_kel;

            }
            $usr_id = $forum1[$i]->user->id;
            $detail_mentor = Models\DetailMentor::where('id_users',$usr_id)->get();
            if(count($detail_mentor)>0){
                if($detail_mentor[0]->url_foto != null && $post[$i]->user->jenis_pengguna != '0'){
                    $arr1 += [
                        'foto_pengirim' => $detail_mentor[0]->url_foto,
                    ];
                }
            }
            $arr1 += [
                'tgl_post' => $forum1[$i]->created_at,
                'jml_like' => $forum1[$i]->jml_like,
                'jml_komen' => $forum1[$i]->jml_komen,
                'video_number' => $video[0]->content->number,
                'video_judul' => $video[0]->judul,
                'video_uuid' => $video[0]->uuid,
                'post_uuid' => $forum1[$i]->uuid
            ];
            $arr00[$i] = $arr1;
        }

        #$result['theme'] = $arr;
        $result = $arr;

        return response()->json([
            'message' => 'Success',
            'max_page' => $max_page,
            'account' => $this->statUser($request->user()),
            'data'    => $result,
            'qna_pagination' => $arr00
        ]);
    }
    public function qnaByUser(Request $request){
        $result = [];
        
        $uuid = $request->user()->uuid;

        if(count($user = Models\User::where('uuid',$uuid)->get())==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        #$forum = Models\Post::where('jenis','forum')->where('stat_post','0')->get();
        
        #$theme = Models\Theme::orderBy('jml_post','DESC')->limit(3)->get();

        $post = Models\Post::where('stat_post',0)
        ->where('id_user',$user[0]->id)
        ->where('jenis','qna')
        ->orderBy('created_at','DESC')->get();

        $arr = [];

        // $post = Controllers\Post\PostController::getPost($qna);
        for($i=0;$i<count($post);$i++){
            $arr1 = [];
            $idTheme = $post[$i]->theme->id;
            $videoTheme = Models\VideoTheme::where('id_theme',$idTheme)->first();
            $video = $videoTheme->video;

            $posting = 'False';
            $like = 'False';
            $alert = 'False';
            $userId = $request->user()->id;

            if($post[$i]->id_user==$userId){
                $posting = 'True';
            }
            if(count($likes = Models\PostLike::where('id_post',$post[$i]->id)
                            ->where('id_user',$userId)->get())>0){
                $like = 'True';
            }
            if(count($alerts = Models\PostAlert::where('id_post',$post[$i]->id)
                            ->where('id_user',$userId)
                            ->where('alert_status',0)->get())>0 || $posting == 'True'){
                $alert = 'True';
            }
            $arr0 = [];

            $st_user = ["new user","data complete","member data complete","admin-mentor"];

            $st_user_post = $st_user[0];
            if($post[$i]->user->jenis_pengguna=='0') {
                if($post[$i]->user->jenis_akun=='0'){
                    $st_user_post = $st_user[0];
                }elseif($post[$i]->user->jenis_akun==2){
                    $st_user_post = $st_user[1];
                }
            }
            if(date_format(date_create($post[$i]->user->tgl_langganan_akhir),"Y/m/d") >= date('Y/m/d')) {
                $st_user_post = $st_user[2];
            }
            if($post[$i]->user->jenis_pengguna==1 || $post[$i]->user->jenis_pengguna==2) {
                $st_user_post = $st_user[3];
            }

            $bad_word = Models\BadWord::select('kata')->get();

            $arr_badWord = [];
            for($k=0;$k<count($bad_word);$k++){
                $arr_badWord[$k] = strtolower($bad_word[$k]->kata);
            }
            $judul = '';
            $juduls = explode(" ", $post[$i]->judul);
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
            $deskripsis = explode(" ", $post[$i]->deskripsi);
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
                'judul' => $judul,
                'user_posting' => $posting,
                'user_like' => $like,
                'user_lapor' => $alert,
                'deskripsi' => $deskripsi,
                'nama_pengirim' => $post[$i]->user->nama,
                'stat_pengirim' => $st_user_post,
            ];

            $det_student = Models\DetailStudent::where('id_users',$post[$i]->id_user)->get();
            if(count($det_student)>0){
                $arr1['jenis_kelamin'] = $det_student[0]->jenis_kel;

            }
            $usr_id = $post[$i]->user->id;
            $detail_mentor = Models\DetailMentor::where('id_users',$usr_id)->get();
            if(count($detail_mentor)>0){
                if($detail_mentor[0]->url_foto != null && $post[$i]->user->jenis_pengguna != '0'){
                    $arr1 += [
                        'foto_pengirim' => $detail_mentor[0]->url_foto,
                    ];
                }
            }
            $arr1 += [
                'tgl_post' => $post[$i]->created_at,
                'jml_like' => $post[$i]->jml_like,
                'jml_komen' => $post[$i]->jml_komen,
                'video_judul' => $video->judul,
                'video_uuid' => $video->uuid,
                'post_uuid' => $post[$i]->uuid
            ];
            $arr[$i] = $arr1;
        }

        #$result['theme'] = $arr;
        $result = $arr;

        return response()->json([
            'message' => 'Success',
            'account' => $this->statUser($request->user()),
            'data'    => $result
        ]);
    }
    public function qnaDetail(Request $request){
        $result = [];

        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        if(count($post = Models\Post::where('uuid',$uuid)
        ->where('jenis','qna')
        ->orderBy('jml_like','DESC')->get())==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        #$forum = Models\Post::where('jenis','forum')->where('stat_post','0')->get();
        
        #$theme = Models\Theme::orderBy('jml_post','DESC')->limit(3)->get();


        // $post = Controllers\Post\PostController::getPost($qna);
        for($i=0;$i<count($post);$i++){
            $arr1 = [];
            $idTheme = $post[$i]->theme->id;
            $videoTheme = Models\VideoTheme::where('id_theme',$idTheme)->first();
            $video = $videoTheme->video;
            $posting = 'False';
            $like = 'False';
            $alert = 'False';
            $userId = $request->user()->id;

            if($post[$i]->id_user==$userId){
                $posting = 'True';
            }
            if(count($likes = Models\PostLike::where('id_post',$post[$i]->id)
                            ->where('id_user',$userId)->get())>0){
                $like = 'True';
            }
            if(count($alerts = Models\PostAlert::where('id_post',$post[$i]->id)
                            ->where('id_user',$userId)
                            ->where('alert_status',0)->get())>0 || $posting == 'True'){
                $alert = 'True';
            }
            $arr0 = [];

            $st_user = ["new user","data complete","member data complete","admin-mentor"];

            $st_user_post = $st_user[0];
            if($post[$i]->user->jenis_pengguna=='0') {
                if($post[$i]->user->jenis_akun=='0'){
                    $st_user_post = $st_user[0];
                }elseif($post[$i]->user->jenis_akun==2){
                    $st_user_post = $st_user[1];
                }
            }
            if(date_format(date_create($post[$i]->user->tgl_langganan_akhir),"Y/m/d") >= date('Y/m/d')) {
                $st_user_post = $st_user[2];
            }
            if($post[$i]->user->jenis_pengguna==1 || $post[$i]->user->jenis_pengguna==2) {
                $st_user_post = $st_user[3];
            }

            $bad_word = Models\BadWord::select('kata')->get();

            $arr_badWord = [];
            for($k=0;$k<count($bad_word);$k++){
                $arr_badWord[$k] = strtolower($bad_word[$k]->kata);
            }
            $judul = '';
            $juduls = explode(" ", $post[$i]->judul);
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
            $deskripsis = explode(" ", $post[$i]->deskripsi);
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
                'judul' => $judul,
                'user_posting' => $posting,
                'user_like' => $like,
                'user_lapor' => $alert,
                'deskripsi' => $deskripsi,
                'nama_pengirim' => $post[$i]->user->nama,
                'stat_pengirim' => $st_user_post,
            ];

            $det_student = Models\DetailStudent::where('id_users',$post[$i]->id_user)->get();
            if(count($det_student)>0){
                $arr1['jenis_kelamin'] = $det_student[0]->jenis_kel;

            }
            $usr_id = $post[$i]->user->id;
            $detail_mentor = Models\DetailMentor::where('id_users',$usr_id)->get();
            if(count($detail_mentor)>0){
                if($detail_mentor[0]->url_foto != null && $post[$i]->user->jenis_pengguna != '0'){
                    $arr1 += [
                        'foto_pengirim' => $detail_mentor[0]->url_foto,
                    ];
                }
            }
            $arr1 += [
                'tgl_post' => $post[$i]->created_at,
                'jml_like' => $post[$i]->jml_like,
                'jml_komen' => $post[$i]->jml_komen,
                'video_judul' => $video->judul,
                'video_uuid' => $video->uuid,
                'post_uuid' => $post[$i]->uuid
            ];
            $arr0[$i] = $arr1;
        }

        $comment = Models\Comment::where('id_post',$post[0]->id)
            ->orderBy('created_at','DESC')->get();

        $arr = [];        
        for($j=0;$j<count($comment);$j++){
            $arr1 = [];
            $user = Models\User::where('id',$comment[$j]->id_user)
                ->first();
            #return $user;

            $comm = 'False';
            $alert = 'False';
            if($comment[$j]->id_user==$request->user()->id){
                $comm = 'True';
            }
            if(count($alerts = Models\CommentAlert::where('id_comment',$comment[$j]->id)
                            ->where('id_user',$request->user()->id)
                            ->where('alert_status',0)->get())>0 || $comm == 'True'){
                $alert = 'True';
            }
            $st_user = ["new user","data complete","member data complete","admin-mentor"];
            $st_user_post = $st_user[0];
            if($comment[$j]->user->jenis_pengguna=='0') {
                if($comment[$j]->user->jenis_akun=='0'){
                    $st_user_post = $st_user[0];
                }elseif($comment[$j]->user->jenis_akun==2){
                    $st_user_post = $st_user[1];
                }
            }
            if(date_format(date_create($comment[$j]->user->tgl_langganan_akhir),"Y/m/d") >= date('Y/m/d')) {
                $st_user_post = $st_user[2];
            }
            if($comment[$j]->user->jenis_pengguna==1 || $comment[$j]->user->jenis_pengguna==2) {
                $st_user_post = $st_user[3];
            }

            $arr1['comment_nama'] = $user->nama;
            $arr1['stat_pengirim'] = $st_user_post;

            $det_student = Models\DetailStudent::where('id_users',$comment[$j]->id_user)->get();
            if(count($det_student)>0){
                $arr1['jenis_kelamin'] = $det_student[0]->jenis_kel;

            }
            $arr1['user_comment'] = $comm;
            $arr1['user_lapor'] = $alert;

            $usr_id = $user->id;
            $detail_mentor = Models\DetailMentor::where('id_users',$usr_id)->get();
            if(count($detail_mentor)>0){
                if($detail_mentor[0]->url_foto != null && $comment[$j]->user->jenis_pengguna != '0'){
                    $arr1 += [
                        'user_foto' => $detail_mentor[0]->url_foto,
                    ];
                }
            }
            $bad_word = Models\BadWord::select('kata')->get();

            $arr_badWord = [];
            for($k=0;$k<count($bad_word);$k++){
                $arr_badWord[$k] = strtolower($bad_word[$k]->kata);
            }
            $judul = '';
            $juduls = explode(" ", $comment[$j]->comment);
            for($l=0;$l<count($juduls);$l++){
                if(in_array(strtolower($juduls[$l]),$arr_badWord)){
                    $len = strlen($juduls[$l]);
                    $juduls[$l] = '';
                    for($m=0;$m<$len;$m++){
                        $juduls[$l] = $juduls[$l].'*';
                    }
                }
                $judul = $judul.$juduls[$l];
                if($l<count($juduls)-1){
                    $judul = $judul.' ';
                }
            }

            $arr1['comment_isi'] = $judul;
            $arr1['comment_tgl'] = $comment[$j]->created_at;
            $arr1['comment_uuid'] = $comment[$j]->uuid;
            $arr[$j] = $arr1;
        }

        $result['posting'] = $arr0;
        $result['comment'] = $arr;

        #$result['theme'] = $arr;

        return response()->json([
            'message' => 'Success',
            'account' => $this->statUser($request->user()),
            'data'    => $result
        ]);
    }

    #=========================QnA===========================

    #=========================Forum===========================
    public function forum(Request $request){
        $result = [];
        #$forum = Models\Post::where('jenis','forum')->where('stat_post','0')->get();
        $video_uuid = Models\Video::select('uuid')->get();
        $theme = Models\Theme::orderBy('urutan','ASC')
                ->whereNotIn('judul',$video_uuid)->get();

        $forum = Models\Post::where('stat_post',0)->where('jenis','forum')
        ->orderBy('created_at','DESC')->get();

        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 10;
        $counter_forum = count($forum);
        $max_page = ceil($counter_forum / $limit);

        $forum1 = Models\Post::where('stat_post',0)->where('jenis','forum')
                ->orderBy('created_at','DESC')
                ->limit($limit)->offset(($page - 1) * $limit)->get();

        $arr = [];
        for($i=0;$i<count($theme);$i++){
            $arr1=[];
            // $forum = Models\Post::where('stat_post',0)->where('jenis','forum')
            //         ->orderBy('jml_like','DESC')->get();
            $arr1 = [
                'urutan' => $theme[$i]->urutan,
                'judul' => $theme[$i]->judul,
                'jml_post' => $theme[$i]->jml_post,
                'theme_image' => $theme[$i]->url_image,
                'theme_uuid' => $theme[$i]->uuid
            ];
            $arr[$i] = $arr1;
        }

        $pos = Controllers\Post\PostController::getPost($forum,$request->user()->id);
        $pos1 = Controllers\Post\PostController::getPost($forum1,$request->user()->id);

        $result['max_page'] = $max_page;
        $result['theme'] = $arr;
        $result['forum'] = $pos;
        $result['forum_pagination'] = $pos1;

        return response()->json([
            'message' => 'Success',
            'account' => $this->statUser($request->user()),
            'data'    => $result
        ]);

    }

    public function forumDetail(Request $request){
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'UUID tidak sesuai'
            ]);
        }
        $result = [];
        if(count($forum = Models\Post::where('jenis','forum')->where('uuid',$uuid)
        ->where('jenis','forum')
        ->where('stat_post','0')->get())==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'UUID tidak sesuai'
            ]);
        }
        if(count($forum)==0){
            return response()->json([
                'message' => 'Success',
                'account' => $this->statUser($request->user()),
                'data'    => $result
            ]);
        }

        $pos = Controllers\Post\PostController::getPost($forum,$request->user()->id);

        $comment = Models\Comment::where('id_post',$forum[0]->id)
            ->orderBy('created_at','DESC')->get();

        $arr = [];        
        for($j=0;$j<count($comment);$j++){
            $arr1 = [];
            $user = Models\User::where('id',$comment[$j]->id_user)
                ->first();
            #return $user;
            $usr_com = 'False';
            $alert = 'False';
            if($comment[$j]->id_user==$request->user()->id){
                $usr_com = 'True';
            }
            if(count($alerts = Models\CommentAlert::where('id_comment',$comment[$j]->id)
                            ->where('id_user',$request->user()->id)
                            ->where('alert_status',0)->get())>0 || $usr_com == 'True'){
                $alert = 'True';
            }

            $st_user = ["new user","data complete","member data complete","admin-mentor"];
            $st_user_post = $st_user[0];
            if($comment[$j]->user->jenis_pengguna=='0') {
                if($comment[$j]->user->jenis_akun=='0'){
                    $st_user_post = $st_user[0];
                }elseif($comment[$j]->user->jenis_akun==2){
                    $st_user_post = $st_user[1];
                }
            }
            if(date_format(date_create($comment[$j]->user->tgl_langganan_akhir),"Y/m/d") >= date('Y/m/d')) {
                $st_user_post = $st_user[2];
            }
            if($comment[$j]->user->jenis_pengguna==1 || $comment[$j]->user->jenis_pengguna==2) {
                $st_user_post = $st_user[3];
            }

            $arr1['comment_nama'] = $user->nama;
            $arr1['stat_pengirim'] = $st_user_post;
            $det_student = Models\DetailStudent::where('id_users',$comment[$j]->id_user)->get();
            if(count($det_student)>0){
                $arr1['jenis_kelamin'] = $det_student[0]->jenis_kel;

            }
            $arr1['user_comment'] = $usr_com;
            $arr1['user_lapor'] = $alert;
            
            $usr_id = $user->id;
            $detail_mentor = Models\DetailMentor::where('id_users',$usr_id)->get();
            if(count($detail_mentor)>0){
                if($detail_mentor[0]->url_foto != null && $user->jenis_pengguna != '0'){
                    $arr1 += [
                        'user_foto' => $detail_mentor[0]->url_foto,
                    ];
                }
            }
            $bad_word = Models\BadWord::select('kata')->get();

            $arr_badWord = [];
            for($k=0;$k<count($bad_word);$k++){
                $arr_badWord[$k] = strtolower($bad_word[$k]->kata);
            }
            $judul = '';
            $juduls = explode(" ", $comment[$j]->comment);
            for($l=0;$l<count($juduls);$l++){
                if(in_array(strtolower($juduls[$l]),$arr_badWord)){
                    $len = strlen($juduls[$l]);
                    $juduls[$l] = '';
                    for($m=0;$m<$len;$m++){
                        $juduls[$l] = $juduls[$l].'*';
                    }
                }
                $judul = $judul.$juduls[$l];
                if($l<count($juduls)-1){
                    $judul = $judul.' ';
                }
            }

            $arr1['comment_isi'] = $judul;
            $arr1['comment_tgl'] = $comment[$j]->created_at;
            $arr1['comment_uuid'] = $comment[$j]->uuid;
            $arr[$j] = $arr1;
        }

        $result['posting'] = $pos;
        $result['comment'] = $arr;

        return response()->json([
            'message' => 'Success',
            'account' => $this->statUser($request->user()),
            'data'    => $result
        ]);
    }

    public function forumByThemePop(Request $request){
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'UUID tidak sesuai'
            ]);
        }
        $result = [];
        if(count($theme = Models\Theme::where('uuid',$uuid)->get())==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'UUID tidak sesuai'
            ]);
        }
        $result = [];
        #$forum = Models\Post::where('jenis','forum')->where('stat_post','0')->get();
        
        $forum = Models\Post::where('id_theme',$theme[0]->id)
        ->where('jenis','forum')
        ->orderBy('jml_like','DESC')->get();
        $arr1 = [
            'judul' => $theme[0]->judul,
            'theme_uuid' => $theme[0]->uuid
        ];
        $result['theme'] = $arr1;

        $pos = Controllers\Post\PostController::getPost($forum,$request->user()->id);

        $result['forum'] = $pos;

        return response()->json([
            'message' => 'Success',
            'account' => $this->statUser($request->user()),
            'data'    => $result
        ]);
    }

    public function forumByThemeNew(Request $request){
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'UUID tidak sesuai'
            ]);
        }
        $result = [];
        if(count($theme = Models\Theme::where('uuid',$uuid)->get())==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'UUID tidak sesuai'
            ]);
        }
        $result = [];
        #$forum = Models\Post::where('jenis','forum')->where('stat_post','0')->get();
        
        $forum = Models\Post::where('id_theme',$theme[0]->id)
        ->where('jenis','forum')
        ->where('stat_post',0)
        ->orderBy('created_at','DESC')->get();
        $arr1 = [
            'judul' => $theme[0]->judul,
            'theme_uuid' => $theme[0]->uuid
        ];
        $result['theme'] = $arr1;

        $pos = Controllers\Post\PostController::getPost($forum,$request->user()->id);

        $result['forum'] = $pos;

        return response()->json([
            'message' => 'Success',
            'account' => $this->statUser($request->user()),
            'data'    => $result
        ]);
    }
    public function forumByUser(Request $request){
        $result = [];
        
        $uuid = $request->user()->uuid;

        $user = Models\User::where('uuid',$uuid)->get();
        
        if(count($user)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        
        $forum = Models\Post::where('id_user',$user[0]->id)
        ->where('jenis','forum')
        ->where('stat_post',0)
        ->orderBy('created_at','DESC')->get();

        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 10;
        $counter_forum = count($forum);
        $max_page = ceil($counter_forum / $limit);

        $forum1 = Models\Post::where('id_user',$user[0]->id)
                ->where('jenis','forum')
                ->where('stat_post',0)
                ->orderBy('created_at','DESC')
                ->limit($limit)->offset(($page - 1) * $limit)->get();


        $pos = Controllers\Post\PostController::getPost($forum,$request->user()->id);
        $pos1 = Controllers\Post\PostController::getPost($forum1,$request->user()->id);

        $result = $pos;
        return response()->json([
            'message' => 'Success',
            'max_page' => $max_page,
            'account' => $this->statUser($request->user()),
            'data'    => $result,
            'forum_pagination' => $pos1

        ]);
    }
    #=========================Forum===========================
    #=========================QnA===========================
    #=========================QnA===========================
    #=========================Testimoni===========================
    public function testimoni(Request $request){
        $result = [];

        $testimoni = Models\Testimoni::limit(10)->get();
        for($i = 0;$i < count($testimoni);$i++){
            $arr = [];

            $user = Models\Testimoni::find($testimoni[$i]->id);
            $arr['nama'] = $user->user->nama;
            $arr['kelas'] = $user->classes->nama;
            $arr['testimoni'] = $testimoni[$i]->testimoni;
            $result[$i] = $arr;
        }


        return response()->json([
            'message' => 'Success',
            'data'    => $result
        ]);
    }
    #=========================Testimoni===========================
}
