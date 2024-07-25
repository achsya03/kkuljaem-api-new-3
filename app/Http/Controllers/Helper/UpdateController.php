<?php

namespace App\Http\Controllers\Helper;

use App\Models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UpdateController extends Controller
{
    public function __construct($pos,$data)
    {
        $this->pos = $pos;
        if($pos=='verifyUser'){
            $this->verifyUser(Models\User::class,$data);
        }elseif($pos=='verifyUserForce'){
            $this->verifyUserForce(Models\User::class,$data);
        }elseif($pos=='changePassUser'){
            $this->changePassUser(Models\User::class,$data);
        }elseif($pos=='changePassUserForce'){
            $this->changePassUserForce(Models\User::class,$data);
        }elseif($pos=='login'){
            $this->login(Models\User::class,$data);
        }elseif($pos=='banner'){
            $this->banner(Models\Banner::class,$data);
        }elseif($pos=='word'){
            $this->word(Models\Words::class,$data);
        }elseif($pos=='video'){
            $this->video(Models\Videos::class,$data);
        }elseif($pos=='classCategory'){
            $this->classCategory(Models\ClassesCategory::class,$data);
        }elseif($pos=='classes'){
            $this->classes(Models\Classes::class,$data);
        }elseif($pos=='content'){
            $this->content(Models\Content::class,$data);
        }elseif($pos=='contentQuiz'){
            $this->contentQuiz(Models\Quiz::class,$data);
        }elseif($pos=='contentVideo'){
            $this->contentVideo(Models\Video::class,$data);
        }elseif($pos=='option'){
            $this->option(Models\Option::class,$data);
        }elseif($pos=='question'){
            $this->question(Models\Question::class,$data);
        }elseif($pos=='contentVideo'){
            $this->contentVideo(Models\Video::class,$data);
        }elseif($pos=='task'){
            $this->task(Models\Task::class,$data);
        }elseif($pos=='teacher'){
            $this->teacher(Models\Teacher::class,$data);
        }elseif($pos=='theme'){
            $this->theme(Models\Theme::class,$data);
        }elseif($pos=='videoTheme'){
            $this->videoTheme(Models\VideoTheme::class,$data);
        }elseif($pos=='post'){
            $this->post(Models\Post::class,$data);
        }elseif($pos=='comment'){
            $this->comment(Models\Comment::class,$data);
        }elseif($pos=='postAlert'){
            $this->postAlert(Models\PostAlert::class,$data);
        }elseif($pos=='commentAlert'){
            $this->commentAlert(Models\CommentAlert::class,$data);
        }elseif($pos=='student'){
            $this->student(Models\Student::class,$data);
        }elseif($pos=='testimoni'){
            $this->testimoni(Models\Testimoni::class,$data);
        }elseif($pos=='userStudent'){
            $this->userStudent(Models\User::class,$data);
        }elseif($pos=='packet'){
            $this->packet(Models\Packet::class,$data);
        }elseif($pos=='reference'){
            $this->reference(Models\Reference::class,$data);
        }elseif($pos=='changePassUserLogin'){
            $this->changePassUserLogin(Models\User::class,$data);
        }elseif($pos=='avatarGroup'){
            $this->avatarGroup(Models\AvatarGroup::class,$data);
        }elseif($pos=='avatar'){
            $this->avatar(Models\Avatar::class,$data);
        }
    }

    private function changePassUser($model,$data){
        $model::where('web_token',$data['old_web_token'])
        ->update([
            'web_token'      => $data['web_token'],
            'password'       => $data['password']
        ]);
    }

    private function changePassUserForce($model,$data){
        $model::where('email',$data['email'])
        ->update([
            // 'web_token'      => $data['web_token'],
            'password'       => $data['password']
        ]);
    }

    private function changePassUserLogin($model,$data){
        $field = [
            //'id_question',
            'password',
            'uuid',
        ];
        for($i=0;$i<count($field)-1;$i++){
            if(isset($data[$field[$i]]) && $data[$field[$i]] != ''){
                Models\User::where('uuid',$data['uuid'])
                ->update([
                    $field[$i]            => $data[$field[$i]]
                ]);
            }
        }
    }

    private function verifyUser($model,$data){
        $model::where('web_token',$data['old_web_token'])
        ->update([
            'email_verified_at'  => DB::raw('CURRENT_TIMESTAMP'),
            'web_token'          => $data['web_token']
        ]);
    }

    private function verifyUserForce($model,$data){
        $model::where('email',$data['email'])
        ->update([
            'email_verified_at'  => DB::raw('CURRENT_TIMESTAMP'),
            // 'web_token'          => $data['web_token']
        ]);
    }

    private function login($model,$data){
        $model::where('email',$data['email'])
        ->update([
            'device_id'       => $data['device_id'],
            'lokasi'          => $data['lokasi']
        ]);
    }

    private function userStudent($data,$data1){
        $field = [
            //'id_question',
            'nama',
            'email',
            'password',
            'url_foto',
            'foto_id',
            'uuid',
        ];
        for($i=0;$i<count($field)-1;$i++){
            if(isset($data[$field[$i]]) && $data[$field[$i]] != ''){
                Models\Users::where('uuid',$data['uuid'])
                ->update([
                    $field[$i]            => $data[$field[$i]]
                ]);
            }
        }
        $field = [
            //'id_question',
            'alamat',
            'jenis_kel',
            'tgl_lahir',
            'tempat_lahir',
            'uuid1',
        ];
        for($i=0;$i<count($field)-1;$i++){
            if(isset($data1[$field[$i]]) && $data1[$field[$i]] != ''){
                Models\DetailStudent::where('uuid',$data1['uuid'])
                ->update([
                    $field[$i]            => $data1[$field[$i]]
                ]);
            }
        }
    }

    private function banner($model,$data){
        $field = [
            //'id_question',
            'judul_banner',
            'url_web',
            'web_id',
            'url_mobile',
            'mobile_id',
            'deskripsi',
            'label',
            'link',
            'uuid',
        ];
        for($i=0;$i<count($field)-1;$i++){
            if(isset($data[$field[$i]]) && $data[$field[$i]] != ''){
                $model::where('uuid',$data['uuid'])
                ->update([
                    $field[$i]            => $data[$field[$i]]
                ]);
            }
        }
    }

    private function word($model,$data){
        $field = [
            //'id_question',
            'jadwal',
            'hangeul',
            'pelafalan',
            'penjelasan',
            'url_pengucapan',
            'pengucapan_id',
            'uuid',
        ];
        for($i=0;$i<count($field)-1;$i++){
            if(isset($data[$field[$i]]) && $data[$field[$i]] != ''){
                $model::where('uuid',$data['uuid'])
                ->update([
                    $field[$i]            => $data[$field[$i]]
                ]);
            }
        }
    }

    private function video($model,$data){
        $field = [
            //'id_question',
            'jadwal',
            'url_video',
            'uuid',
        ];
        for($i=0;$i<count($field)-1;$i++){
            if(isset($data[$field[$i]]) && $data[$field[$i]] != ''){
                $model::where('uuid',$data['uuid'])
                ->update([
                    $field[$i]            => $data[$field[$i]]
                ]);
            }
        }
    }

    private function contentVideo($model,$data){
        $field = [
            //'id_question',
            'jadwal',
            'judul',
            'keterangan',
            'jml_latihan',
            'jml_shadowing',
            'url_video',
            'uuid',
        ];
        for($i=0;$i<count($field)-1;$i++){
            if(isset($data[$field[$i]]) && $data[$field[$i]] != ''){
                $model::where('uuid',$data['uuid'])
                ->update([
                    $field[$i]            => $data[$field[$i]]
                ]);
            }
        }
    }

    private function classCategory($model,$data){
        $model::where('uuid',$data['uuid'])
        ->update([
            'nama'            => $data['nama'],
            'deskripsi'       => $data['deskripsi']
        ]);
    }

    private function classes($model,$data){
        $field = [
            'id_class_category',
            'nama',
            'deskripsi',
            'url_web',
            'web_id',
            'url_mobile',
            'mobile_id',
            'jml_video',
            'jml_kuis',
            'status_tersedia',
            'uuid',
        ];
        for($i=0;$i<count($field)-1;$i++){
            if(isset($data[$field[$i]]) && $data[$field[$i]] != ''){
                $model::where('uuid',$data['uuid'])
                ->update([
                    $field[$i]            => $data[$field[$i]]
                ]);
            }
        }
        
    }
    
    private function contentQuiz($model,$data){
        $field = [
            'judul',
            'keterangan',
            'jml_pertanyaan',
            'uuid',
        ];
        for($i=0;$i<count($field)-1;$i++){
            if(isset($data[$field[$i]]) && $data[$field[$i]] != ''){
                $model::where('uuid',$data['uuid'])
                ->update([
                    $field[$i]            => $data[$field[$i]]
                ]);
            }
        }
    }

    // private function contentVideo($model,$data){
    //     $model::where('uuid',$data['uuid'])
    //     ->update([
    //         'judul'                        => $data['judul'],
    //         'keterangan'                    => $data['keterangan'],
    //         'url_video'                    => $data['url_video']
    //     ]);
    // }

    private function option($model,$data){
        $field = [
            //'id_question',
            'jawaban_teks',
            'url_gambar',
            'gambar_id',
            'url_file',
            'file_id',
            'uuid',
        ];
        for($i=0;$i<count($field)-1;$i++){
            if(isset($data[$field[$i]]) && $data[$field[$i]] != ''){
                $model::where('uuid',$data['uuid'])
                ->update([
                    $field[$i]            => $data[$field[$i]]
                ]);
            }
        }
    }

    private function question($model,$data){
        $field = [
            //'id_question',
            'pertanyaan_teks',
            'url_gambar',
            'gambar_id',
            'jenis_jawaban',
            'url_file',
            'file_id',
            'jawaban',
            'uuid',
        ];
        for($i=0;$i<count($field)-1;$i++){
            if(isset($data[$field[$i]]) && $data[$field[$i]] != ''){
                $model::where('uuid',$data['uuid'])
                ->update([
                    $field[$i]            => $data[$field[$i]]
                ]);
            }
        }
    }

    private function task($model,$data){
        $model::where('uuid',$data['uuid'])
        ->update([
            'id_question'            => $data['id_question'],
            'id_video'               => $data['id_video'],
            'number'                 => $data['number']
        ]);
    }

    private function teacher($model,$data){
        $model::where('uuid',$data['uuid'])
        ->update([
            'id_user'             => $data['id_user'],
            'id_class'            => $data['id_class']
        ]);
    }

    private function theme($model,$data){
        $field = [
            //'id_question',
            'judul',
            'jml_post',
            'url_gambar',
            'gambar_id',
            'jml_like',
            'jml_comment',
            'uuid',
        ];
        for($i=0;$i<count($field)-1;$i++){
            if(isset($data[$field[$i]]) && $data[$field[$i]] != ''){
                $model::where('uuid',$data['uuid'])
                ->update([
                    $field[$i]            => $data[$field[$i]]
                ]);
            }
        }
    }

    private function videoTheme($model,$data){
        $field = [
            //'id_question',
            'id_video',
            'id_theme',
            'uuid',
        ];
        for($i=0;$i<count($field)-1;$i++){
            if(isset($data[$field[$i]]) && $data[$field[$i]] != ''){
                $model::where('uuid',$data['uuid'])
                ->update([
                    $field[$i]            => $data[$field[$i]]
                ]);
            }
        }
    }

    private function post($model,$data){
        $field = [
            //'id_question',
            'id_user',
            'id_theme',
            'judul',
            'jenis',
            'deskripsi',
            'jml_like',
            'jml_komen',
            'stat_post',
            'stat_terpilih',
            'uuid',
        ];
        for($i=0;$i<count($field)-1;$i++){
            if(isset($data[$field[$i]]) && $data[$field[$i]] != ''){
                // if($data[$field[$i]]==0){
                //     $data[$field[$i]]='0';
                // }
                $model::where('uuid',$data['uuid'])
                ->update([
                    $field[$i]            => $data[$field[$i]]
                ]);
            }
        }
    }

    private function comment($model,$data){
        $field = [
            //'id_question',
            'id_user',
            'id_post',
            'comment',
            'stat_comment',
            'uuid',
        ];
        for($i=0;$i<count($field)-1;$i++){
            if(isset($data[$field[$i]]) && $data[$field[$i]] != ''){
                $model::where('uuid',$data['uuid'])
                ->update([
                    $field[$i]            => $data[$field[$i]]
                ]);
            }
        }
    }

    private function postAlert($model,$data){
        $field = [
            //'id_question',
            'id_user',
            'id_post',
            'komentar',
            'alert_status',
            'uuid',
        ];
        for($i=0;$i<count($field)-1;$i++){
            if(isset($data[$field[$i]]) && $data[$field[$i]] != ''){
                $model::where('uuid',$data['uuid'])
                ->update([
                    $field[$i]            => $data[$field[$i]]
                ]);
            }
        }
    }

    private function commentAlert($model,$data){
        $field = [
            //'id_question',
            'id_user',
            'id_comment',
            'komentar',
            'alert_status',
            'uuid',
        ];
        for($i=0;$i<count($field)-1;$i++){
            if(isset($data[$field[$i]]) && $data[$field[$i]] != ''){
                $model::where('uuid',$data['uuid'])
                ->update([
                    $field[$i]            => $data[$field[$i]]
                ]);
            }
        }
    }

    private function student($model,$data){
        $field = [
            //'id_question',
            'id_user',
            'id_class',
            'register_date',
            'jml_pengerjaan',
            'uuid',
        ];
        for($i=0;$i<count($field)-1;$i++){
            if(isset($data[$field[$i]]) && $data[$field[$i]] != ''){
                $model::where('uuid',$data['uuid'])
                ->update([
                    $field[$i]            => $data[$field[$i]]
                ]);
            }
        }
    }

    private function packet($model,$data){
        $field = [
            //'id_question',
            'lama_paket',
            'harga',
            'status_aktif',
            'uuid',
        ];
        for($i=0;$i<count($field)-1;$i++){
            if(isset($data[$field[$i]]) && $data[$field[$i]] != ''){
                $model::where('uuid',$data['uuid'])
                ->update([
                    $field[$i]            => $data[$field[$i]]
                ]);
            }
        }
    }

    private function testimoni($model,$data){
        $model::where('uuid',$data['uuid'])
        ->update([
            'id_class'           => $data['id_class'],
            'id_user'            => $data['id_user'],
            'tgl_testimoni'      => $data['tgl_testimoni'],
            'testimoni'          => $data['testimoni']
        ]);
    }

    private function avatarGroup($model,$data){
        $model::where('uuid',$data['uuid'])
        ->update([
            'nama'           => $data['nama'],
            'deskripsi'      => $data['deskripsi']
        ]);
    }

    private function avatar($model,$data){
        $field = [
            //'id_question',
            'nama',
            'deskripsi',
            'avatar_url',
            'avatar_id',
            'uuid',
        ];
        for($i=0;$i<count($field)-1;$i++){
            if(isset($data[$field[$i]]) && $data[$field[$i]] != ''){
                $model::where('uuid',$data['uuid'])
                ->update([
                    $field[$i]            => $data[$field[$i]]
                ]);
            }
        }
    }

    private function reference($model,$data){
        $field = [
            //'id_question',
            'nama',
            'kode',
            'tgl_aktif',
            'status',
            'uuid',
        ];
        for($i=0;$i<count($field)-1;$i++){
            if(isset($data[$field[$i]]) && $data[$field[$i]] != ''){
                $model::where('uuid',$data['uuid'])
                ->update([
                    $field[$i]            => $data[$field[$i]]
                ]);
            }
        }
    }
}
