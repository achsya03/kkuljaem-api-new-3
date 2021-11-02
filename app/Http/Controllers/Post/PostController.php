<?php

namespace App\Http\Controllers\Post;

use App\Models;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Notification;
use App\Http\Controllers\FCMController;
use Illuminate\Http\Request;
use App\Http\Controllers\Helper;
use Validator;

class PostController extends Controller
{

    public function __construct(Request $request){
        $this->middleware('auth');
    }
    
    private function checkPostNum($user,$type)
    {
        $post = Models\Post::where('id_user',$user)
                ->whereDate('created_at',date('Y/m/d'))
                ->where('jenis',$type)->get();
        return count($post);
    }
    private function checkCommentNum($user,$type)
    {
        $comment = Models\Comment::join('post','post.id','=','comment.id_post')
                ->where('post.id_user',$user)
                ->whereDate('post.created_at',date('Y/m/d'))
                ->where('post.jenis',$type)->get();
        return count($comment);
    }
    public function addQnAPost(Request $request)
    {
        $result = [];
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        
        $video = Models\Video::where('uuid',$uuid)->get();
        if(count($video)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        $test = $this->checkPostNum($request->user()->id,'qna');

        // if($test >= 5){
        //     return response()->json([
        //         'message' => 'Failed',
        //         'error' => 'Anda sudah posting 5 kali hari ini'
        //     ]);
        // }

        $theme = Models\Theme::where('judul',$video[0]->uuid)->get();
        //return $theme;
        if(count($theme) == 0){
            #input theme
            $validation = new Helper\ValidationController('theme');
            $uuid1 = $validation->data['uuid'];

            $data = [
                'judul'         => $video[0]->uuid,
                'jml_post'      => 0,
                'jml_like'      => 0,
                'jml_comment'   => 0,
                'uuid'          => $uuid1
            ];

            $input = new Helper\InputController('theme',$data);

            $theme = Models\Theme::where('uuid',$uuid1)->get();

            $validation2 = new Helper\ValidationController('videoTheme');
            $uuid2 = $validation2->data['uuid'];
            #input video theme
            $data = [
                'id_video'  => $video[0]->id,
                'id_theme'  => $theme[0]->id,
                'uuid'      => $uuid2
            ];
            $input = new Helper\InputController('videoTheme',$data);
        }

        $validation3 = new Helper\ValidationController('post');
        
        $this->rules = $validation3->rules;
        $this->messages = $validation3->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        //$return_data=$validator->validated();
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }
        $uuid3 = $validation3->data['uuid'];

        #input post
        $data = [
            'id_user' => $request->user()->id,
            'id_theme' => $theme[0]->id,
            'judul' => $request->judul,
            'jenis' => 'qna',
            'deskripsi' => $request->deskripsi,
            'jml_like' => 0,
            'jml_komen' => 0,
            'stat_post' => 0,
            'stat_terpilih' => 0,
            'uuid' => $uuid3
        ];
        $input = new Helper\InputController('post',$data);

        #update theme
        $data = [
            'jml_post'  => $theme[0]->jml_post+1,
            'uuid'      => $theme[0]->uuid
        ];
        $update = new Helper\UpdateController('theme',$data);

        $post = Models\Post::where('uuid',$uuid3)->get();
        $posts = $this->getPost($post,$request->user()->id);

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'info'    => 'Proses Input QnA Berhasil',
            'data'    => $posts[0]
        ]);
    }

    public function qnaTheme(Request $request)
    {
        $class_category = Models\ClassesCategory::all();
        $result = [];
        $arr = [];
        for($i=0;$i<count($class_category);$i++){
            $arr1 = [];
            $arr11 = [];

            $arr1['category_nama'] = $class_category[$i]->nama;
            $arr1['category_uuid'] = $class_category[$i]->uuid;

            $classes = Models\Classes::where('id_class_category',$class_category[$i]->id)
                        ->where('status_tersedia',1)->get();

            for($j=0;$j<count($classes);$j++){
                $arr2 = [];
                $arr22 = [];

                $arr2['class_nama'] = $classes[$j]->nama;
                $arr2['class_uuid'] = $classes[$j]->uuid;

                $content = Models\Content::where('id_class',$classes[$j]->id)
                            ->where('type','video')
                            ->orderBy('number','ASC')->get();
                for($k=0;$k<count($content);$k++){
                    $arr3 = [
                        'video_nomor' => $content[$k]->number,
                        'video_judul' => $content[$k]->video[0]->judul,
                        'video_uuid' => $content[$k]->video[0]->uuid
                    ];
                    $arr22[$k] = $arr3;
                }
                $arr2['videos'] = $arr22;
                $arr11[$j] = $arr2;
            }
            $arr1['classes'] = $arr11;
            $result[$i] = $arr1;
        }
        
        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'data'    => $result
        ]);
    }

    public function addForumPost(Request $request)
    {
        $result = [];
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        
        $theme = Models\Theme::where('uuid',$uuid)->get();
        if(count($theme)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        $test = $this->checkPostNum($request->user()->id,'forum');

        if($test >= 5){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Anda sudah posting 5 kali hari ini'
            ]);
        }
        if(isset($request->post_image)){
            if(count($request->post_image)>3){
                return response()->json([
                    'message' => 'Failed',
                    'error' => 'Gambar Yang di upload maksimal 3'
                ]);
            }
        }

        $validation3 = new Helper\ValidationController('post');
        
        $this->rules = $validation3->rules;
        $this->messages = $validation3->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        //$return_data=$validator->validated();
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }
        $stat_post = '0';
        $uuid3 = $validation3->data['uuid'];
        if(isset($request->post_terpilih)){
            if(!in_array($request->post_terpilih,[0,1])){
                return response()->json([
                    'message' => 'Failed',
                    'error' => 'Post Terpilih tidak sesuai[0|1]'
                ]);
            }else{
                $stat_post = $request->post_terpilih;
            }
        }

        #input post
        $data = [
            'id_user' => $request->user()->id,
            'id_theme' => $theme[0]->id,
            'judul' => $request->judul,
            'jenis' => 'forum',
            'deskripsi' => $request->deskripsi,
            'jml_like' => 0,
            'jml_komen' => 0,
            'stat_post' => 0,
            'stat_terpilih' => $stat_post,
            'uuid' => $uuid3
        ];

        $input = new Helper\InputController('post',$data);

        $post = Models\Post::where('uuid',$uuid3)->first();
        if(isset($request->post_image)){
            for($i=0;$i<count($request->post_image);$i++){
                #input post
                $validation4 = new Helper\ValidationController('postImage');
                $uuid4 = $validation4->data['uuid'];

                if($request->post_image[$i]!=null || $request->post_image[$i]!=''){
                    $gambar1 = $request->post_image[$i];
                    $uploadedFileUrl1 = $validation4->UUidCheck($gambar1,'Post/Forum');
                    $data = [
                        'id_post'       => $post->id,
                        'url_gambar'    => $uploadedFileUrl1['getSecurePath'],
                        'gambar_id'     => $uploadedFileUrl1['getPublicId'],
                        'uuid'          => $uuid4
                    ];
                    $input = new Helper\InputController('postImage',$data);
                }
            }
        }
        #update theme
        $data = [
            'jml_post'  => $theme[0]->jml_post+1,
            'uuid'      => $theme[0]->uuid
        ];
        $update = new Helper\UpdateController('theme',$data);

        $post = Models\Post::where('uuid',$uuid3)->get();
        $posts = $this->getPost($post,$request->user()->id);

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'info'    => 'Proses Input Forum Berhasil',
            'data'    => $posts[0]
        ]);
    }

    public function updateForumPost(Request $request)
    {
        $result = [];
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        
        $post = Models\Post::where('uuid',$uuid)->get();
        if(count($post)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        if($request->user()->jenis_pengguna == 0){
            $test = $this->checkPostNum($request->user()->id,'forum');

            if($test >= 5){
                return response()->json([
                    'message' => 'Failed',
                    'error' => 'Anda sudah posting 5 kali hari ini'
                ]);
            }
            if(isset($request->post_image)){
                if(count($request->post_image)>3){
                    return response()->json([
                        'message' => 'Failed',
                        'error' => 'Gambar Yang di upload maksimal 3'
                    ]);
                }
            }
        }

        $validation3 = new Helper\ValidationController('post');
        
        $this->rules = $validation3->rules;
        $this->messages = $validation3->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        //$return_data=$validator->validated();
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }
        $stat_post = '0';
        $uuid3 = $uuid;
        if(isset($request->post_terpilih)){
            if(!in_array($request->post_terpilih,[0,1])){
                return response()->json([
                    'message' => 'Failed',
                    'error' => 'Post Terpilih tidak sesuai[0|1]'
                ]);
            }else{
                $stat_post = $request->post_terpilih;
            }
        }

        #update post
        $data = [
            //'id_user' => $request->user()->id,
            'id_theme' => $theme[0]->id,
            'judul' => $request->judul,
            //'jenis' => 'forum',
            'deskripsi' => $request->deskripsi,
            //'jml_like' => 0,
            //'jml_komen' => 0,
            //'stat_post' => 0,
            'stat_terpilih' => $stat_post,
            'uuid' => $uuid3
        ];

        $input = new Helper\UpdateController('post',$data);

        $post = Models\Post::where('uuid',$uuid3)->first();
        if(isset($request->post_image)){
            $post_image = Models\PostImage::where('id_post',$post[0]->id)->delete();
            for($i=0;$i<count($request->post_image);$i++){
                #input post
                $validation4 = new Helper\ValidationController('postImage');
                $uuid4 = $validation4->data['uuid'];

                $gambar1 = $request->post_image[$i];
                $uploadedFileUrl1 = $validation4->UUidCheck($gambar1,'Post/Forum');
                $data = [
                    'id_post'       => $post->id,
                    'url_gambar'    => $uploadedFileUrl1['getSecurePath'],
                    'gambar_id'     => $uploadedFileUrl1['getPublicId'],
                    'uuid'          => $uuid4
                ];
                $input = new Helper\InputController('postImage',$data);
            }
        }
        

        $post = Models\Post::where('uuid',$uuid3)->get();
        $posts = $this->getPost($post,$request->user()->id);

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'info'    => 'Proses Input Forum Berhasil',
            'data'    => $posts[0]
        ]);
    }

    public function deletePost(Request $request)
    {
        $result = [];
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        
        $post = Models\Post::where('uuid',$uuid)->get();
        if(count($post)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        $post_image = $post[0]->postImage;
        for($i=0;$i<count($post_image);$i++){
            Cloudinary::destroy($post_image[$i]->gambar_id);
        }

        #delete post
        $delete = Models\Post::where('uuid',$uuid)->delete();
        
        #update theme
        $data = [
            'jml_post'  => $post[0]->theme->jml_post-1,
            'uuid'      => $post[0]->theme->uuid
        ];
        $update = new Helper\UpdateController('theme',$data);

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'info'    => 'Proses Hapus Post Berhasil'
        ]);
    }

    public function addComment(Request $request)
    {
        $result = [];
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        
        $post = Models\Post::where('uuid',$uuid)->get();
        if(count($post)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        if($post[0]->jenis=='forum'){
            $comment = $this->checkCommentNum($request->user()->id,'forum');
            if($comment>=5){               
                return response()->json([
                    'message' => 'Failed',
                    'error' => 'Anda sudah memberi komentar pada forum 5 kali hari ini'
                ]);
            }
        }

        $validation = new Helper\ValidationController('comment');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        //$return_data=$validator->validated();
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        $uuid1 = $validation->data['uuid'];
        #input comment
        $data = [
            'id_user' => $request->user()->id,
            'id_post' => $post[0]->id,
            'comment' => $request->komentar,
            'stat_comment' => 0,
            'uuid' => $uuid1
        ];
        
        $input = new Helper\InputController('comment',$data);


        #update post
        $data = [
            'jml_komen'  => $post[0]->jml_komen+1,
            'uuid'       => $post[0]->uuid
        ];
        $update = new Helper\UpdateController('post',$data);


        #update theme
        $data = [
            'jml_comment'  => $post[0]->theme->jml_comment+1,
            'uuid'         => $post[0]->theme->uuid
        ];
        $update = new Helper\UpdateController('theme',$data);

        $post = Models\Post::where('uuid',$uuid)->get();
        $posts = $this->getPost($post,$request->user()->id);

        $comment = Models\Comment::where('id_post',$post[0]->id)
                    ->orderBy('created_at','DESC')->get();

        $arr = [];        
        for($j=0;$j<count($comment);$j++){
            $arr1 = [];
            $user = Models\User::where('id',$comment[$j]->id_user)
                ->first();
            #return $user;

            $comm = 'False';
            if($comment[$j]->id_user==$request->user()->id){
                $comm = 'True';
            }

            $arr1['comment_nama'] = $user->nama;
            $arr1['user_comment'] = $comm;
            if($user->jenis_pengguna!='0'){
                $arr1['user_foto'] = $user->url_foto;
            }
            $arr1['comment_isi'] = $comment[$j]->comment;
            $arr1['comment_tgl'] = $comment[$j]->created_at;
            $arr1['comment_uuid'] = $comment[$j]->uuid;
            $arr[$j] = $arr1;
        }

        $datas = [];
        if($post[0]->jenis == 'forum'){
            $datas = [
                'user_uuid'       => $post[0]->user->uuid,
                'judul'           => 'Postingan mendapat komentar baru dari '.$request->user()->nama,
                'deskripsi'       => $request->komentar,
                'posisi'          => 'Forum-Comment',
                //'gambar'          => $datas['gambar'],
                'uuid_target'     => $post[0]->uuid,
                'maker_uuid'     => $request->user()->uuid,
                'uuid'            => $validation->data['uuid'],
            ];
        }elseif($post[0]->jenis == 'qna'){
            $datas = [
                'user_uuid'       => $post[0]->user->uuid,
                'judul'           => 'Pertanyaanmu mendapat tanggapan baru dari '.$request->user()->nama,
                'deskripsi'       => $request->komentar,
                'posisi'          => 'QnA-Comment',
                //'gambar'          => $datas['gambar'],
                'uuid_target'     => $post[0]->uuid,
                'maker_uuid'     => $request->user()->uuid,
                'uuid'            => $validation->data['uuid'],
            ];
        }
        //return $datas;

        $add_notif = Notification\NotificationController::addData($datas);
        $push_notif = FCMController::sendNotification($post[0]->user,$datas);
        $err = '';
        $push_json = json_decode($push_notif);
        if($push_json->success==0){
            $err = 'Push Tidak Bisa Dilakukan';
        }

        $result['posting'] = $posts;
        $result['comment'] = $arr;

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'info'    => 'Proses Input Komentar Berhasil',
            'data'    => $result
        ]);
    }

    public function deleteComment(Request $request)
    {
        $result = [];
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        
        $comment = Models\Comment::where('uuid',$uuid)->get();
        if(count($comment)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        #delete comment
        $delete = Models\Comment::where('uuid',$uuid)->delete();
        
        #update post
        $data = [
            'jml_komen'  => $comment[0]->post->jml_komen-1,
            'uuid'       => $comment[0]->post->uuid
        ];
        $aa = Models\Post::where('uuid',$data['uuid'])
        ->update([
            'jml_komen'            => $data['jml_komen']
        ]);


        #update theme
        $data = [
            'jml_comment'  => $comment[0]->post->theme->jml_comment-1,
            'uuid'         => $comment[0]->post->theme->uuid
        ];
        $aa = Models\Theme::where('uuid',$data['uuid'])
        ->update([
            'jml_comment'            => $data['jml_comment']
        ]);
        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'info'    => 'Proses Hapus Comment Berhasil'
        ]);
    }

    public function alertPost(Request $request)
    {
        $result = [];
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        
        $post = Models\Post::where('uuid',$uuid)->get();
        if(count($post)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        $validation = new Helper\ValidationController('postAlert');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        //$return_data=$validator->validated();
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        $post_alert = Models\PostAlert::select('alert_status')
                        ->where('id_user',$request->user()->id)
                        ->where('id_post',$post[0]->id)
                        ->where('alert_status','0')->get();

        if(count($post_alert)==0){
            $uuid1 = $validation->data['uuid'];
            #input alertPost
            $data = [
                'id_user' => $request->user()->id,
                'id_post' => $post[0]->id,
                'komentar' => $request->komentar,
                'alert_status' => 0,
                'uuid' => $uuid1
            ];

            $input = new Helper\InputController('postAlert',$data);
            return response()->json([
                'message' => 'Success',
                //'account' => $this->statUser($request->user()),
                'info'    => 'Proses Input Alert Post Berhasil'
            ]);
        }elseif(count($post_alert)>0){
            return response()->json([
                'message' => 'Success',
                //'account' => $this->statUser($request->user()),
                'info'    => 'Input Tidak Dilakukan Karena Terdapat Laporan Aktif'
            ]);
        }
    }

    public function alertPostDelete(Request $request){
        $result = [];
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        
        $post = Models\Post::where('uuid',$uuid)->get();
        if(count($post)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        #delete post
        $delete = Models\PostAlert::where('id_post',$post[0]->id)
                ->where('id_user',$request->user()->id)->delete();

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'info'    => 'Proses Hapus Alert Post Berhasil'
        ]);
    }

    public function alertComment(Request $request)
    {
        $result = [];
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        
        $comment = Models\Comment::where('uuid',$uuid)->get();
        if(count($comment)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        $validation = new Helper\ValidationController('commentAlert');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        //$return_data=$validator->validated();
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        $comment_alert = Models\CommentAlert::select('alert_status')
                        ->where('id_user',$request->user()->id)
                        ->where('id_comment',$comment[0]->id)
                        ->where('alert_status','0')->get();

                        //return $comment_alert;
        if(count($comment_alert)==0){
            $uuid1 = $validation->data['uuid'];

            #input alertPost
            $data = [
                'id_user' => $request->user()->id,
                'id_comment' => $comment[0]->id,
                'komentar' => $request->komentar,
                'alert_status' => 0,
                'uuid' => $uuid1
            ];

            $input = new Helper\InputController('commentAlert',$data);

            return response()->json([
                'message' => 'Success',
                //'account' => $this->statUser($request->user()),
                'info'    => 'Proses Input Alert Comment Berhasil'
            ]);
        }elseif(count($comment_alert)>0){
            return response()->json([
                'message' => 'Success',
                //'account' => $this->statUser($request->user()),
                'info'    => 'Input Tidak Dilakukan Karena Terdapat Laporan Aktif'
            ]);
        }
    }
  
    public function alertCommentDelete(Request $request){
        $result = [];
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        
        $comment = Models\Comment::where('uuid',$uuid)->get();
        if(count($comment)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        #delete comment
        $delete = Models\CommentAlert::where('id_comment',$comment[0]->id)
                ->where('id_user',$request->user()->id)->delete();

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'info'    => 'Proses Hapus Alert Comment Berhasil'
        ]);
    }  
  
    public function addLike(Request $request){
        $result = [];
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        
        $post = Models\Post::where('uuid',$uuid)->get();
        if(count($post) == 0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        $postLike = Models\PostLike::where('id_user',$request->user()->id)
                    ->where('id_post',$post[0]->id)->get();
        if(count($postLike)>0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Like Masih Aktif'
            ]);
        }
        $validation = new Helper\ValidationController('postLike');
        // $this->rules = $validation->rules;
        // $this->messages = $validation->messages;

        // $validator = Validator::make($request->all(), $this->rules, $this->messages);
        // #echo $web_token;
        // //$return_data=$validator->validated();
        // if($validator->fails()){
        //     return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        // }

        $uuid1 = $validation->data['uuid'];
        #input Like
        $data = [
            'id_user' => $request->user()->id,
            'id_post' => $post[0]->id,
            'uuid' => $uuid1
        ];

        $input = new Helper\InputController('postLike',$data);

        #update post
        $data = [
            'jml_like'  => $post[0]->jml_like+1,
            'uuid'       => $post[0]->uuid
        ];
        $update = new Helper\UpdateController('post',$data);


        #update theme
        $data = [
            'jml_like'  => $post[0]->theme->jml_like+1,
            'uuid'         => $post[0]->theme->uuid
        ];
        $update = new Helper\UpdateController('theme',$data);

        $datas = [];
        if($post[0]->jenis == 'forum'){
            $datas = [
                'user_uuid'       => $post[0]->user->uuid,
                'judul'           => 'Postinganmu disukai oleh '.$request->user()->nama,
                'deskripsi'       => 'Postinganmu disukai oleh '.$request->user()->nama,
                'posisi'          => 'Forum-Like',
                //'gambar'          => $datas['gambar'],
                'uuid_target'     => $post[0]->uuid,
                'maker_uuid'     => $request->user()->uuid,
                'uuid'            => $validation->data['uuid'],
            ];
        }elseif($post[0]->jenis == 'qna'){
            $datas = [
                'user_uuid'       => $post[0]->user->uuid,
                'judul'           => 'Pertanyaanmu disukai oleh '.$request->user()->nama,
                'deskripsi'       => 'Pertanyaanmu disukai oleh '.$request->user()->nama,
                'posisi'          => 'QnA-Like',
                //'gambar'          => $datas['gambar'],
                'uuid_target'     => $post[0]->uuid,
                'maker_uuid'     => $request->user()->uuid,
                'uuid'            => $validation->data['uuid'],
            ];
        }
        //return $datas;

        $add_notif = Notification\NotificationController::addData($datas);
        $push_notif = FCMController::sendNotification($post[0]->user,$datas);
        $err = '';
        $push_json = json_decode($push_notif);
        if($push_json->success==0){
            $err = 'Push Tidak Bisa Dilakukan';
        }

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'info'    => 'Proses Input Like Berhasil '.$err
        ]);
    } 

    public function deleteLike(Request $request){
        $result = [];
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        
        $post = Models\Post::where('uuid',$uuid)->get();
        if(count($post[0]->postLike)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Like Belum Dilakukan'
            ]);
        }

        #delete like post
        $delete = Models\PostLike::where('id_post',$post[0]->id)
                ->where('id_user',$request->user()->id)->delete();

        #update post
        $data = [
            'jml_like'  => $post[0]->jml_like-1,
            'uuid'       => $post[0]->uuid
        ];
        $aa = Models\Post::where('uuid',$data['uuid'])
        ->update([
            'jml_like'            => $data['jml_like']
        ]);

        //return $aa;
        //$update = new Helper\UpdateController('post',$data);


        #update theme
        $data = [
            'jml_like'  => $post[0]->theme->jml_like-1,
            'uuid'         => $post[0]->theme->uuid
        ];
        $aa = Models\Theme::where('uuid',$data['uuid'])
        ->update([
            'jml_like'            => $data['jml_like']
        ]);
        //$update = new Helper\UpdateController('theme',$data);

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'info'    => 'Proses Hapus Like Berhasil'
        ]);
    }

    public static function getPost($post,$userId){
        $pos = [];
        for($i = 0;$i < count($post); $i++){
            $arr1 = [];
            $arr_img = [];
            for($j = 0;$j < count($post[$i]->postImage);$j++){
                $arr3 = [];
                $arr3['url_gambar'] = $post[$i]->postImage[$j]->url_gambar;
                $arr3['gambar_uuid'] = $post[$i]->postImage[$j]->uuid;
                $arr_img[$j] = $arr3;
            }
            $arr1 = $arr_img;
            $posting = 'False';
            $like = 'False';
            $alert = 'False';
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

            if($post[$i]->jenis == 'forum'){
                $pos[$i] = [
                    'judul' => $judul,
                    'user_posting' => $posting,
                    'user_like' => $like,
                    'user_lapor' => $alert,
                    'deskripsi' => $deskripsi,
                    'tema' => $post[$i]->theme->judul,
                    'nama_pengirim' => $post[$i]->user->nama,
                    'stat_pengirim' => $st_user_post,
                ];
                $det_student = Models\DetailStudent::where('id_users',$post[$i]->id_user)->get();
                if(count($det_student)>0){
                    $pos[$i]['jenis_kelamin'] = $det_student[0]->jenis_kel;

                }
            }elseif($post[$i]->jenis == 'qna'){
                $vid = Models\Video::where('uuid',$post[$i]->theme->judul)->first();
                $pos[$i] = [
                    'judul' => $judul,
                    'user_posting' => $posting,
                    'user_like' => $like,
                    'user_lapor' => $alert,
                    'deskripsi' => $deskripsi,
                    'video_judul' => $vid->judul,
                    'nama_pengirim' => $post[$i]->user->nama,
                    'stat_pengirim' => $st_user_post,
                ];

                $det_student = Models\DetailStudent::where('id_users',$post[$i]->user->id)->get();
                if(count($det_student)>0){
                    $pos[$i]['jenis_kelamin'] = $det_student[0]->jenis_kel;

                }
            }
                if($post[$i]->user->url_foto != null && $post[$i]->user->jenis_pengguna!='0'){
                    $pos[$i] += [
                        'foto_pengirim' => $post[$i]->user->url_foto,
                    ];
                }
            $pos[$i] += [
                'tgl_post' => $post[$i]->created_at,
                'jml_like' => $post[$i]->jml_like,
                'jml_komen' => $post[$i]->jml_komen,
            ];
                if($arr1 != null){
                    $pos[$i] += [
                        'gambar' => $arr1,
                    ];
                }
            $pos[$i] += [
                'post_uuid' => $post[$i]->uuid
            ];
        }

        return $pos;
    }
}
