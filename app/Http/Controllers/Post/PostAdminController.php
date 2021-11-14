<?php

namespace App\Http\Controllers\Post;

use App\Models;
use App\Http\Controllers\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class PostAdminController extends Controller
{
    #========================Forum===============================

    public function listForum(Request $request)
    {
        if(($uuid = $request->token) == null){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        if(!$theme = Models\Theme::where('uuid',$uuid)->first()){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        $forum_selected = Models\Post::where('jenis','forum')
                            ->where('id_theme',$theme->id)
                            ->where('stat_terpilih',1)
                            ->get();
        $forum_unselected = Models\Post::where('jenis','forum')
                            ->where('id_theme',$theme->id)
                            ->where('stat_terpilih',0)
                            ->get();
        
        $arr1 = [];
        for($i=0;$i<count($forum_selected);$i++){
            $status = 'Publik';
            if($forum_selected[$i]->stat_post == 1){
                $status = 'Disembunyikan';
            }
            
            $arr11 = [
                'status' => $status,
                'judul' => $forum_selected[$i]->judul,
                'deskripsi' => $forum_selected[$i]->judul,
                'user_post' => $forum_selected[$i]->user->nama,
                'created_at' => $forum_selected[$i]->created_at,
                'jml_like' => $forum_selected[$i]->jml_like,
                'jml_komen' => $forum_selected[$i]->jml_komen,
                'post_uuid' => $forum_selected[$i]->uuid
            ];
            $arr1[$i] = $arr11;
        }

        $arr2 = [];
        for($i=0;$i<count($forum_unselected);$i++){
            $status = 'Publik';
            if($forum_unselected[$i]->stat_post == 1){
                $status = 'Disembunyikan';
            }
            
            $arr22 = [
                'status' => $status,
                'judul' => $forum_unselected[$i]->judul,
                'deskripsi' => $forum_unselected[$i]->judul,
                'user_post' => $forum_unselected[$i]->user->nama,
                'created_at' => $forum_unselected[$i]->created_at,
                'jml_like' => $forum_unselected[$i]->jml_like,
                'jml_komen' => $forum_unselected[$i]->jml_komen,
                'post_uuid' => $forum_unselected[$i]->uuid
            ];
            $arr2[$i] = $arr22;
        }
        
        $result = [
            'theme' => $theme,
            'selected_forum' => $arr1,
            'unselected_forum' => $arr2
        ];

        unset($theme['id']);
        $theme['theme_uuid'] = $theme['uuid'];
        unset($theme['uuid']);

        return response()->json([
            'message'=>'Success',
            'data' => $result
        ]);
    }

    public function listQnA(Request $request)
    {
        
        $qna = Models\Post::where('jenis','qna')
                            //->where('stat_post',0)
                            ->get();
        
        $arr1 = [];
        for($i=0;$i<count($qna);$i++){
            $status = 'Publik';
            if($qna[$i]->stat_post == 1){
                $status = 'Disembunyikan';
            }
            $judul_theme = $qna[$i]->theme->judul;

            $video = Models\Video::where('uuid',$judul_theme)
                    //->where('stat_post',0)
                    ->first();
            $classes = Models\Classes::where('id',$video->content->id_class)
                    //->where('stat_post',0)
                    ->first();
                        
            $arr11 = [
                'status' => $status,
                'judul' => $qna[$i]->judul,
                'deskripsi' => $qna[$i]->deskripsi,
                'video_judul' => $video->judul,
                'video_uuid' => $video->uuid,
                'class_nama' => $classes->nama,
                'class_uuid' => $classes->uuid,
                'user_post' => $qna[$i]->user->nama,
                'created_at' => $qna[$i]->created_at,
                'jml_like' => $qna[$i]->jml_like,
                'jml_komen' => $qna[$i]->jml_komen,
                'post_uuid' => $qna[$i]->uuid
            ];
            $arr1[$i] = $arr11;
        }

        $result = $arr1;


        return response()->json([
            'message'=>'Success',
            'data' => $result
        ]);
    }

    public function listClasses(Request $request)
    {
        
        $classes = Models\Classes::orderBy('nama','ASC')->get();
            
        
        $arr1 = [];
        $content = Models\Content::where('id_class',$classes[$i]->id)
                        ->where('type','video')->orderBy('number','ASC')->get();

        $arr11 = [];
        $arr10 = [];
              
        for($j = 0;$j<count($content);$j++){
            $arr10['video_nama'] = $content[$j]->video[0]->judul;
            $arr10['video_episode'] = $content[$j]->number;
            $arr10['video_uuid'] = $content[$j]->video[0]->uuid;
            $arr10['class_nama'] = $classes[$i]->nama;
            $arr10['class_uuid'] = $classes[$i]->uuid;
            $arr11[$j] = $arr10;
        }

        $result = $arr11;


        return response()->json([
            'message'=>'Success',
            'data' => $result
        ]);
    }

    public function selectForum(Request $request)
    {
        
        if(($action = $request->action) == null || !in_array($request->action, ['add','remove']) || ($uuid = $request->token) == null){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        if(!$post = Models\Post::where('uuid',$uuid)->first()){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        if($post->stat_post == 1){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Post Telah Diblokir'
            ]);
        }

        $stat = 0;
        if($request->action == 'add'){
            $stat = 1;
        }if($request->action == 'remove'){
            $stat = '0';
        }

        #update post
        $data = [
            'stat_terpilih' => $stat,
            'uuid' => $uuid
        ];

        $input = new Helper\UpdateController('post',$data);

        return response()->json([
            'message'=>'Success',
            'info' => 'Proses Update Berhasil'
        ]);
    }
        
    #========================Forum===============================
}
