<?php

namespace App\Http\Controllers\Classes;

use App\Models;
use App\Models\Classes;
use App\Models\ClassesCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Helper;
use Validator;

// use League\Flysystem\Filesystem;
// use Spatie\Dropbox\Client;
// use Spatie\FlysystemDropbox\DropboxAdapter;

class ClassController extends Controller
{

    public function checkData(Request $request)
    {
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        if(count($classes = Classes::where('uuid',$uuid)->get())==0){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        $content = Models\Content::where('id_class',$classes[0]->id)->get();
        $result['nomor_content'] = count($content)+1;

        return response()->json(['message'=>'Success','data'
        => $result]);
    }

    public function detailDataForClass(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        if(count(ClassesCategory::where('uuid',$uuid)->get())==0){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $limit = 5;

        if(isset($request->limit)){
            $limit = $request->limit;
        }

        $class_cat = ClassesCategory::where('uuid',$uuid)->first();
        $classes = Classes::where('id_class_category',$class_cat->id)->orderBy('urutan','ASC')
                //->where('nama','LIKE','%'.$request->nama_kelas.'%')
                // ->where('nama','ilike','%'.$request->nama_kelas.'%')
                // ->limit($limit)->offset(($request->page - 1) * $limit)
                ->get();
        
        $result = [];
        if(count($classes)==0){
            return response()->json(['message'=>'Success','data'
            => $result]);
        }
        // foreach ($classes as $cl) {
        //     unset($cl['id']);
        //     unset($cl['id_class_category']);
        // }
        $res= $this->classesValue($classes);
        $result['nama_group'] = $res[0]['nama_group'];
        $result['group_urutan'] = $res[0]['group_urutan'];
        $result['group_deskripsi'] = $res[0]['group_deskripsi'];
        $result['group_uuid'] = $res[0]['group_uuid'];

        $result['classes'] = $res;
        for ($i=0;$i<count($res);$i++){
            unset($result['classes'][$i]['mentor_not_reg']);
            unset($result['classes'][$i]['group']);
            unset($result['classes'][$i]['group_all']);
            unset($result['classes'][$i]['nama_group']);
            //unset($result['classes'][$i]['mentor']);
            unset($result['classes'][$i]['group_deskripsi']);
            unset($result['classes'][$i]['url_web']);
            unset($result['classes'][$i]['url_mobile']);
            unset($result['classes'][$i]['group_uuid']);
        }

        return response()->json(['message'=>'Success','data'
        => $result]);
    }

    public function addData(Request $request)
    {
        $validation = new Helper\ValidationController('classes');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        //$return_data=$validator->validated();
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $id_class_category = ClassesCategory::where('uuid',$uuid)->first();

        if(!$id_class_category){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        
        // $id_class_category = ClassesCategory::where('uuid',$request->id_class_category)->first();
        // if(!$id_class_category){
        //     return response()->json(['message'=>'Failed','info'=>'Category ID Tidak Sesuai']);
        // }
        for($i=0;$i<count($request->id_user);$i++){
            if(!$user = Models\User::where('uuid',$request->id_user[$i])
                            ->where('jenis_pengguna','!=',0)->first()){
                return response()->json(['message'=>'Failed','info'=>'User ID Tidak Sesuai']);
            }
        }

        $uploadedFileUrl1 = [
            'getSecurePath' => '',
            'getPublicId'   => ''
        ];

        $uploadedFileUrl2 = $uploadedFileUrl1;

        $gambar1 = $request->url_web;
        $uploadedFileUrl1 = $validation->UUidCheck($gambar1,'Class/Web');

        $gambar2 = $request->url_mobile;
        $uploadedFileUrl2 = $validation->UUidCheck($gambar2,'Class/Mobile');

        $uuid1 = $validation->data['uuid'];
        
        $data = [
            'id_class_category'          => $id_class_category->id,
            'nama'                       => request('judul'),
            'deskripsi'                  => request('deskripsi'),
            'url_web'                    => $uploadedFileUrl1['getSecurePath'],
            'web_id'                     => $uploadedFileUrl1['getPublicId'],
            'url_mobile'                 => $uploadedFileUrl2['getSecurePath'],
            'mobile_id'                  => $uploadedFileUrl2['getPublicId'],
            'jml_video'                  => 0,
            'jml_kuis'                   => 0,
            'status_tersedia'            => request('status_tersedia'),
            'uuid'                       => $uuid1
        ];

        $input = new Helper\InputController('classes',$data);

        $classes = Models\Classes::where('uuid',$uuid1)->first();
        $validation1 = new Helper\ValidationController('teacher');
        for($i=0;$i<count($request->id_user);$i++){
            $user = Models\User::where('uuid',$request->id_user[$i])->first();
            $uuid2 = $validation1->data['uuid'];
            $data = [
                'id_user'                => $user->id,
                'id_class'               => $classes->id,
                'uuid'                   => $uuid2
            ];
            $input = new Helper\InputController('teacher',$data);
        }

        return response()->json(['message'=>'Success','info'
        => 'Proses Input Berhasil']);
    }

    public function updateData(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $validation = new Helper\ValidationController('classes');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;


        $classes = Classes::where('uuid',$uuid)->first();

        if(!$classes){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        $id_class_category = ClassesCategory::where('uuid',$request->id_class_category)->first();
        if(!$id_class_category){
            return response()->json(['message'=>'Failed','info'=>'ID Category tidak valid']);
        }

        $uploadedFileUrl1 = [
            'getSecurePath' => '',
            'getPublicId'   => ''
        ];
        $uploadedFileUrl2 = $uploadedFileUrl1;

        if(isset($request->url_web)){
            $uploadedFileUrl1 = $validation->UUidCheck($request->url_web,'Class/Web');
            if(isset($classes->web_id) && $classes->web_id != ''){
                $validation->deleteImage($classes->web_id);
            }
        }

        if(isset($request->url_mobile)){
            $uploadedFileUrl2 = $validation->UUidCheck($request->url_mobile,'Class/Mobile');
            if(isset($classes->mobile_id) && $classes->mobile_id != ''){
                $validation->deleteImage($classes->mobile_id);
            }
        }

        $data = [
            'id_class_category'          => $id_class_category->id,
            'nama'                       => request('judul'),
            'deskripsi'                  => request('deskripsi'),
            'url_web'                    => $uploadedFileUrl1['getSecurePath'],
            'web_id'                     => $uploadedFileUrl1['getPublicId'],
            'url_mobile'                 => $uploadedFileUrl2['getSecurePath'],
            'mobile_id'                  => $uploadedFileUrl2['getPublicId'],
            'jml_video'                  => 0,
            'jml_kuis'                   => 0,
            'status_tersedia'            => request('status_tersedia'),
            'uuid'                       => $uuid
        ];

        $input = new Helper\UpdateController('classes',$data);

        $classes = Models\Classes::where('uuid',$uuid)->first();            
        $tc = Models\Teacher::where('id_class',$classes->id)->forceDelete();

        $validation1 = new Helper\ValidationController('teacher');
        for($i=0;$i<count($request->id_user);$i++){
            $user = Models\User::where('uuid',$request->id_user[$i])->first();
            $uuid2 = $validation1->data['uuid'];
            $data = [
                'id_user'                => $user->id,
                'id_class'               => $classes->id,
                'uuid'                   => $uuid2
            ];
            $input = new Helper\InputController('teacher',$data);
        }

        return response()->json(['message'=>'Success','info'
        => 'Proses Update Berhasil']);
    }

    public function deleteData(Request $request)
    {
        $result = [];
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        
        $classes = Models\Classes::where('uuid',$uuid)->get();
        if(count($classes)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        if($classes[0]->status_tersedia==1){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Kelas Masih Aktif'
            ]);
        }

        $content = $content = Models\Content::where('id_class',$classes[0]->id)->get();
        for($j=0;$j<count($content);$j++){
            if($content[$j]->type=='video'){
                $task = Models\Task::where('id_video',$content[$j]->video[0]->id)->get();

                for($k=0;$k<count($task);$k++){
                    $question = Models\Question::where('id',$task[$k]->id_question)->delete();    
                }
                $task = Models\Task::where('id_video',$content[$j]->video[0]->id)->delete();
                $theme = Models\Theme::where('judul',$content[$j]->video[0]->uuid)->delete();

                $shadowing = Models\Shadowing::where('id_video',$content[$j]->video[0]->id)->get();
                for($k=0;$k<count($shadowing);$k++){
                    $word = Models\Words::where('id',$shadowing[$k]->id_word)->delete();
                }

                $shadowing = Models\Shadowing::where('id_video',$content[$j]->video[0]->id)->delete();
            }elseif($content[$j]->type=='quiz'){
                $quiz = Models\Exam::where('id_quiz',$content[$j]->quiz[0]->id)->get();
                for($k=0;$k<count($quiz);$k++){
                    $question = Models\Question::where('id',$quiz[$k]->id_question)->delete();
                }
                $quiz = Models\Exam::where('id_quiz',$content[$j]->quiz[0]->id)->delete();
            }
        }
        $content = Models\Content::where('id_class',$classes[0]->id)
                    ->delete();


        #delete classes
        $delete = Models\Classes::where('uuid',$uuid)->delete();
        
        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'info'    => 'Proses Hapus Kelas Berhasil'
        ]);
    }

    #testing
    public function allData(Request $request){

        $classes = Classes::where('nama','LIKE','%'.$request->nama_kelas.'%')->orderBy('urutan','ASC')
        ->limit($request->limit)->get();
        // foreach ($classes as $cl) {
        //     unset($cl['id']);
        //     unset($cl['id_class_category']);
        // }
        
        $result = $this->classesValue($classes);
        for ($i=0;$i<count($result);$i++){
            unset($result[$i]['mentor_not_reg']);
            unset($result[$i]['mentor_all']);
            unset($result[$i]['group_all']);
        }

        return response()->json(['message'=>'Success','data'
        => $result]);
    }

    public function getForAddData(Request $request){

        $category = Models\ClassesCategory::all();
        $mentor = Models\User::where('jenis_pengguna','!=',0)->get();
        for($i=0;$i<count($category);$i++){
            $category[$i]['category_uuid'] = $category[$i]->uuid;
            unset($category[$i]->id);
            unset($category[$i]->deskripsi);
            unset($category[$i]->uuid);
        }

        $arrMentor = [];
        for($i=0;$i<count($mentor);$i++){
            $arr = [];
            $arrMentor[$i] = [
                'nama' => $mentor[$i]->nama,
                'mentor_uuid' => $mentor[$i]->uuid,
            ];
        }       

        $result = [
            //'category' => $category,
            'mentor' => $arrMentor
        ];

        return response()->json(['message'=>'Success','data'
        => $result]);
    }

    public function detailData(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        if(count($classes = Classes::where('uuid',$uuid)->get())==0){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $result = $this->classesValue($classes);
        for ($i=0;$i<count($result);$i++){
            unset($result[$i]['group']);
            //unset($result[$i]['mentor']);
            // unset($result[$i]['group_all']);
        }

        return response()->json(['message'=>'Success','data'
        => $result[0]]);
    }

    public function studentData(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        if(count($classes = Classes::where('uuid',$uuid)->get())==0){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $limit = 5;

        if(isset($request->limit)){
            $limit = $request->limit;
        }

        $student = Models\Student::join('users','users.id','=','students.id_user')
                ->where('id_class',$classes[0]->id)
                //->where('nama','ilike','%'.$request->nama_siswa.'%')
                // ->where('users.nama','LIKE','%'.$request->nama_siswa.'%')
                // ->limit($limit)->offset(($request->page - 1) * $limit)
                ->get();
                //return $student;
        $result = [];
        for($i=0;$i<count($student);$i++){
            $arr = [];
            $progress = ($student[$i]->jml_pengerjaan / ($classes[0]->jml_video+$classes[0]->jml_kuis))*100;

            $arr = [
                'nama' => $student[$i]->user->nama,
                'progress' => $progress,
                'uuid' => $student[$i]->uuid,
            ];
            $result[$i] = $arr;
        }


        return response()->json(['message'=>'Success','data'
        => $result]);
    }

    public function classContent(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        if(count($classes = Classes::where('uuid',$uuid)->get())==0){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $result = [];
        $result = $this->classesValue($classes);

        for ($i=0;$i<count($result);$i++){
            unset($result[$i]['group_all']);
            unset($result[$i]['group']);
            unset($result[$i]['mentor_not_reg']);
            unset($result[$i]['mentor_all']);
            unset($result[$i]['dibuat']);
            unset($result[$i]['diubah']);
        }
        $limit = 5;

        if(isset($request->limit)){
            $limit = $request->limit;
        }

        $content = Models\Content::where('id_class',$classes[0]->id)
                    //->where('video.judul','ilike','%'.$request->nama_kelas.'%')
                    // ->where('video.judul','LIKE','%'.$request->judul.'%')
                    // //->where('quiz.judul','ilike','%'.$request->nama_quiz.'%')
                    // ->where('quiz.judul','LIKE','%'.$request->judul.'%')
                    //->limit($limit)->offset(($request->page - 1) * $limit)
                    ->orderBy('number','ASC')->get();
        if(isset($request->judul_video)){
            $content = Models\Content::join('video','content.id','=','video.id_content')
            ->where('id_class',$classes[0]->id)
            // ->where('video.judul','ilike','%'.$request->nama_kelas.'%')
            ->where('video.judul','LIKE','%'.$request->judul_video.'%')
            // //->where('quiz.judul','ilike','%'.$request->nama_quiz.'%')
            // ->where('quiz.judul','LIKE','%'.$request->judul.'%')
            //->limit($limit)->offset(($request->page - 1) * $limit)
            ->orderBy('number','ASC')->get();
        }elseif(isset($request->judul_quiz)){
            $content = Models\Content::join('quiz','content.id','=','quiz.id_content')
            ->where('id_class',$classes[0]->id)
            //->where('video.judul','ilike','%'.$request->nama_kelas.'%')
            // ->where('video.judul','LIKE','%'.$request->judul.'%')
            //->where('quiz.judul','ilike','%'.$request->nama_quiz.'%')
            ->where('quiz.judul','LIKE','%'.$request->judul_quiz.'%')
            //->limit($limit)->offset(($request->page - 1) * $limit)
            ->orderBy('number','ASC')->get();
        }
        //return $content;
        $arr = [];
        $arr9 = [];
        $arr8 = [];
        $co1=0;$co2=0;
        for ($i=0;$i<count($content);$i++){
            $arr0 = [];
            $arr0['number'] = $content[$i]->number;
            if($content[$i]->type == 'quiz'){
                $quiz = Models\Quiz::where('id_content',$content[$i]->id)->first();
                if(isset($request->judul_quiz)){
                    $quiz = Models\Quiz::where('id_content',$content[$i]->id_content)->first();
                }
                $test = $quiz;
                //return $content[$i]->id;
                $arr0['jenis'] = $content[$i]->type;
                $arr0['judul'] = $quiz->judul;
                $arr0['keterangan'] = $quiz->keterangan;
                $arr0['jml_pertanyaan'] = $quiz->jml_pertanyaan;
                $arr0['uuid'] = $quiz->uuid;
                $arr9[$co1] = $arr0;
                $co1 += 1;
            }
            if($content[$i]->type == 'video'){
                $video = Models\Video::where('id_content',$content[$i]->id)->first();
                if(isset($request->judul_video)){
                    $video = Models\Video::where('id_content',$content[$i]->id_content)->first();
                }
                $arr0['jenis'] = $content[$i]->type;
                $arr0['judul'] = $video->judul;
                $arr0['keterangan'] = $video->keterangan;
                $arr0['jml_latihan'] = $video->jml_latihan;
                $arr0['jml_shadowing'] = $video->jml_shadowing;
                $arr0['uuid'] = $video->uuid;
                $arr8[$co2] = $arr0;
                $co2 += 1;
            }
            $arr[$i] = $arr0;
        }

        // $result[0]['jml_materi'] = count($arr);
        // $result[0]['jml_video'] = count($arr8);
        // $result[0]['jml_quiz'] = count($arr9);
        $result[0]['materi'] = $arr;
        $result[0]['video'] = $arr8;
        $result[0]['quiz'] = $arr9;
        
        return response()->json(['message'=>'Success','data'
        => $result[0]]);
    }

    private function classesValue($classes){
        $result = [];
        $statTersedia = ['Draft','Public'];
        for($i=0;$i<count($classes);$i++){
            $mentor =[];
            $idMentor = [];
            for($j=0;$j<count($classes[$i]->teacher);$j++){
                $arr = [];
                $idMentor[$j] = $classes[$i]->teacher[$j]->id_user;
                $arr['nama_mentor'] = $classes[$i]->teacher[$j]->user->nama;
                $arr['mentor_uuid'] = $classes[$i]->teacher[$j]->uuid;
                $arr['user_uuid'] = $classes[$i]->teacher[$j]->user->uuid;
                $mentor[$j] = $arr;
            }
            $users = Models\User::where('jenis_pengguna','!=',0)->get();
            $arr0 = [];
            for($j=0;$j<count($users);$j++){
                $arr = [];
                $arr['nama_mentor'] = $users[$j]->nama;
                //$arr['mentor_terpilih'] = 'Tidak Terpilih';
                for($k=0;$k<count($classes[$i]->teacher);$k++){
                    if($classes[$i]->teacher[$k]->id_user == $users[$j]->id){
                        //$arr['mentor_terpilih'] = 'Terpilih';
                        break;
                    }
                }
                $arr['user_uuid'] = $users[$j]->uuid;
                $arr0[$j] = $arr;
            }
            $cat = Models\ClassesCategory::all();
            $arr01 = [];
            for($j=0;$j<count($cat);$j++){
                $arr = [];
                $arr['nama_group'] = $cat[$j]->nama;
                $arr['grup_terpilih'] = 'Tidak Terpilih';
                if($classes[$i]->id_class_category == $cat[$j]->id){
                    $arr['grup_terpilih'] = 'Terpilih';
                }
                $arr['group_uuid'] = $cat[$j]->uuid;
                $arr01[$j] = $arr;
            }

            $result[$i] = [
                'nama_group' => $classes[$i]->class_category->nama,
                'group_deskripsi' => $classes[$i]->class_category->deskripsi,
                'group_urutan' => $classes[$i]->class_category->urutan,
                'group_uuid' => $classes[$i]->class_category->uuid,
                'group' => $arr01,
                'judul_class' => $classes[$i]->nama,
                'urutan_class' => $classes[$i]->urutan,
                'deskripsi_class' => $classes[$i]->deskripsi,
                'mentor' => $mentor,
                'mentor_all' => $arr0,
                'url_web' => $classes[$i]->url_web,
                'url_mobile' => $classes[$i]->url_mobile,
                'jml_video' => $classes[$i]->jml_video,
                'jml_quiz' => $classes[$i]->jml_kuis,
                'jml_materi' => $classes[$i]->jml_kuis + $classes[$i]->jml_video,
                'dibuat' => $classes[$i]->created_at,
                'status' => $statTersedia[$classes[$i]->status_tersedia],
                'diubah' => $classes[$i]->updated_at,
                'class_uuid' => $classes[$i]->uuid,
            ];
        }
        return $result;
    }
}
