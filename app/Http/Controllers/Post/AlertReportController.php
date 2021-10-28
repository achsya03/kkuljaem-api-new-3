<?php

namespace App\Http\Controllers\Post;

use App\Models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AlertReportController extends Controller
{
    public function userReport(Request $request){
        if(!$status = $request->status){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Status tidak sesuai'
            ]);
        }
        
        $arr_stat = ['waiting','accepted','ignored','all'];
        if(!in_array($status,$arr_stat)){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Status tidak sesuai'
            ]);
        }
        $all_report = [];
        $qna_report = [];
        $forum_report = [];
        $comment_report = [];
        $st = array_search($status, $arr_stat);

        $post_alert = Models\PostAlert::where('alert_status',$st)->get();
        $comment_alert = Models\CommentAlert::where('alert_status',$st)->get();

        if($status=='all'){
            $post_alert = Models\PostAlert::all();
            $comment_alert = Models\CommentAlert::all();
        }
        
        
        $all_counter = 0;
        $arr0 = [];
        for($i=0;$i<count($post_alert);$i++){
            $komentar = "";
            $status = "";
            //$jenis = $post_alert[$i]->post->jenis;
            //return $post_alert[$i]->post;
            if($post_alert[$i]->post->jenis == 'forum'){
                $komentar = "Posting Forum - ";
            }elseif($post_alert[$i]->post->jenis == 'qna'){
                $komentar = "Posting QnA - ";
            }

            if($post_alert[$i]->alert_status == 0){
                $status = "Menunggu Konfirmasi";
            }elseif($post_alert[$i]->alert_status == 1){
                $status = "Diterima";
            }elseif($post_alert[$i]->alert_status == 2){
                $status = "Ditolak";
            }

            $arr = [
                'user_lapor'=>$post_alert[$i]->user->nama,
                'komentar'=>$komentar.$post_alert[$i]->komentar,
                'tgl_lapor'=>$post_alert[$i]->created_at,
                'status'=>$status,
                'jenis'=>$post_alert[$i]->post->jenis,
                'post_report_uuid'=>$post_alert[$i]->uuid,
            ];

            $all_report[$all_counter] = $arr;
            $all_counter += 1;
        }

        for($i=0;$i<count($comment_alert);$i++){
            $komentar = "";
            $status = "";
            //$jenis = $comment_alert[$i]->post->jenis;

            $id_post = $comment_alert[$i]->comment->id_post;
            $posts = Models\Post::where('id',$id_post)->first();
            if($posts->jenis == 'forum'){
                $komentar = "Comment Forum - ";
            }elseif($posts->jenis == 'qna'){
                $komentar = "Comment QnA - ";
            }

            if($comment_alert[$i]->alert_status == 0){
                $status = "Menunggu Konfirmasi";
            }elseif($comment_alert[$i]->alert_status == 1){
                $status = "Diterima";
            }elseif($comment_alert[$i]->alert_status == 2){
                $status = "Ditolak";
            }

            $arr = [
                'user_lapor'=>$comment_alert[$i]->user->nama,
                'komentar'=>$komentar.$comment_alert[$i]->komentar,
                'tgl_lapor'=>$comment_alert[$i]->created_at,
                'status'=>$status,
                'jenis'=>$posts->jenis,
                'comment_report_uuid'=>$comment_alert[$i]->uuid,
            ];

            $all_report[$all_counter] = $arr;
            $all_counter += 1;
        }

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'data'    => $all_report
        ]);
    }

    public function detailReport(Request $request){
        if(!($uuid = $request->token) || !($type = $request->type)){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Parameter tidak sesuai'
            ]);
        }
        
        $arr_stat = ['waiting','accepted','ignored'];
        $arr_type = ['posting','comment'];
        if(!in_array($type,$arr_type)){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Parameter tidak sesuai'
            ]);
        }
        $all_report = [];
        $qna_report = [];
        $forum_report = [];
        $comment_report = [];
        $all_counter = 0;
        
        if($type == 'posting'){
            if(count($post_alert = Models\PostAlert::where('uuid',$uuid)->get())==0){
                return response()->json([
                    'message' => 'Failed',
                    'error' => 'Parameter tidak sesuai'
                ]);
            }
            
            $arr0 = [];
            for($i=0;$i<count($post_alert);$i++){
                $komentar = "";
                $status = "";
                //$jenis = $post_alert[$i]->post->jenis;
                //return $post_alert[$i]->post;
                if($post_alert[$i]->post->jenis == 'forum'){
                    $komentar = "Posting Forum - ";
                }elseif($post_alert[$i]->post->jenis == 'qna'){
                    $komentar = "Posting QnA - ";
                }

                if($post_alert[$i]->status == 0){
                    $status = "Menunggu Konfirmasi";
                }elseif($post_alert[$i]->status == 1){
                    $status = "Diterima";
                }elseif($post_alert[$i]->status == 2){
                    $status = "Ditolak";
                }

                $arr = [
                    'jenis'=>$post_alert[$i]->post->jenis,
                    'judul'=>$post_alert[$i]->post->judul,
                    'deskripsi'=>$post_alert[$i]->post->deskripsi,
                    'user_lapor'=>$post_alert[$i]->user->nama,
                    //'komentar'=>$komentar.$post_alert[$i]->komentar,
                    'tgl_lapor'=>$post_alert[$i]->created_at,
                    'status'=>$status,
                    'post_report_uuid'=>$post_alert[$i]->uuid,
                ];

                $all_report[$all_counter] = $arr;
                $all_counter += 1;
            }
        }
        elseif($type == 'comment'){
            if(count($comment_alert = Models\CommentAlert::where('uuid',$uuid)->get())==0){
                return response()->json([
                    'message' => 'Failed',
                    'error' => 'Parameter tidak sesuai'
                ]);
            }
            for($i=0;$i<count($comment_alert);$i++){
                $komentar = "";
                $status = "";
                //$jenis = $comment_alert[$i]->post->jenis;

                $id_post = $comment_alert[$i]->comment->id_post;
                $posts = Models\Post::where('id',$id_post)->first();
                if($posts->jenis == 'forum'){
                    $komentar = "Comment Forum";
                }elseif($posts->jenis == 'qna'){
                    $komentar = "Comment QnA";
                }

                if($comment_alert[$i]->status == 0){
                    $status = "Menunggu Konfirmasi";
                }elseif($comment_alert[$i]->status == 1){
                    $status = "Diterima";
                }elseif($comment_alert[$i]->status == 2){
                    $status = "Ditolak";
                }

                $arr = [
                    'jenis'=>$posts->jenis,
                    'judul'=>$komentar,
                    'deskripsi'=>$comment_alert[$i]->comment->comment,
                    'user_lapor'=>$comment_alert[$i]->user->nama,
                    //'komentar'=>$komentar.' - '.$comment_alert[$i]->komentar,
                    'tgl_lapor'=>$comment_alert[$i]->created_at,
                    'status'=>$status,
                    'comment_report_uuid'=>$comment_alert[$i]->uuid,
                ];

                $all_report[$all_counter] = $arr;
                $all_counter += 1;
            }
        }

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'data'    => $all_report
        ]);
    }


    public function updateReport(Request $request){
        if(!($uuid = $request->token) || !($type = $request->type) || !($value = $request->value)){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Parameter tidak sesuai'
            ]);
        }
        
        $arr_stat = ['waiting','accepted','ignored'];
        $arr_type = ['posting','comment'];
        if(!in_array($type,$arr_type) || !in_array($value,$arr_stat)){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Parameter tidak sesuai'
            ]);
        }
        $all_report = [];
        $qna_report = [];
        $forum_report = [];
        $comment_report = [];
        $all_counter = 0;
        
        if($type == 'posting'){
            if(count($post_alert = Models\PostAlert::where('uuid',$uuid)->get())==0){
                return response()->json([
                    'message' => 'Failed',
                    'error' => 'Laporan Tidak Ditemukan'
                ]);
            }
            Models\PostAlert::where('uuid',$uuid)
                ->update([
                    'alert_status'            => array_search($value, $arr_stat)
                ]);

            if(array_search($value, $arr_stat) == 1){
                Models\Post::where('id',$post_alert[0]->id_post)
                    ->update([
                        'stat_post'            => '1'
                    ]);
            }else{
                Models\Post::where('id',$post_alert[0]->id_post)
                    ->update([
                        'stat_post'            => '0'
                    ]);
            }
            
            $arr0 = [];
            $post_alert = Models\PostAlert::where('uuid',$uuid)->get();
            for($i=0;$i<count($post_alert);$i++){
                $komentar = "";
                $status = "";
                //$jenis = $post_alert[$i]->post->jenis;
                //return $post_alert[$i]->post;
                if($post_alert[$i]->post->jenis == 'forum'){
                    $komentar = "Posting Forum - ";
                }elseif($post_alert[$i]->post->jenis == 'qna'){
                    $komentar = "Posting QnA - ";
                }

                if($post_alert[$i]->alert_status == 0){
                    $status = "Menunggu Konfirmasi";
                }elseif($post_alert[$i]->alert_status == 1){
                    $status = "Diterima";
                }elseif($post_alert[$i]->alert_status == 2){
                    $status = "Ditolak";
                }

                $arr = [
                    'jenis'=>$post_alert[$i]->post->jenis,
                    'judul'=>$post_alert[$i]->post->judul,
                    'deskripsi'=>$post_alert[$i]->post->deskripsi,
                    'user_lapor'=>$post_alert[$i]->user->nama,
                    //'komentar'=>$komentar.$post_alert[$i]->komentar,
                    'tgl_lapor'=>$post_alert[$i]->created_at,
                    'status'=>$status,
                    'post_report_uuid'=>$post_alert[$i]->uuid,
                ];

                $all_report[$all_counter] = $arr;
                $all_counter += 1;
            }
        }
        elseif($type == 'comment'){
            if(count($comment_alert = Models\CommentAlert::where('uuid',$uuid)->get())==0){
                return response()->json([
                    'message' => 'Failed',
                    'error' => 'Laporan Tidak Ditemukan'
                ]);
            }
            Models\CommentAlert::where('uuid',$uuid)
                ->update([
                    'alert_status'            => array_search($value, $arr_stat)
                ]);

            if(array_search($value, $arr_stat) == 1){
                Models\Comment::where('id',$comment_alert[0]->id_comment)
                    ->update([
                        'stat_comment'            => '1'
                    ]);
            }else{
                Models\Comment::where('id',$comment_alert[0]->id_comment)
                    ->update([
                        'stat_comment'            => '0'
                    ]);
            }

            $comment_alert = Models\CommentAlert::where('uuid',$uuid)->get();
            for($i=0;$i<count($comment_alert);$i++){
                $komentar = "";
                $status = "";
                //$jenis = $comment_alert[$i]->post->jenis;

                $id_post = $comment_alert[$i]->comment->id_post;
                $posts = Models\Post::where('id',$id_post)->first();
                if($posts->jenis == 'forum'){
                    $komentar = "Comment Forum";
                }elseif($posts->jenis == 'qna'){
                    $komentar = "Comment QnA";
                }

                if($comment_alert[$i]->alert_status == 0){
                    $status = "Menunggu Konfirmasi";
                }elseif($comment_alert[$i]->alert_status == 1){
                    $status = "Diterima";
                }elseif($comment_alert[$i]->alert_status == 2){
                    $status = "Ditolak";
                }

                $arr = [
                    'jenis'=>$posts->jenis,
                    'judul'=>$komentar,
                    'deskripsi'=>$comment_alert[$i]->comment->comment,
                    'user_lapor'=>$comment_alert[$i]->user->nama,
                    //'komentar'=>$komentar.' - '.$comment_alert[$i]->komentar,
                    'tgl_lapor'=>$comment_alert[$i]->created_at,
                    'status'=>$status,
                    'comment_report_uuid'=>$comment_alert[$i]->uuid,
                ];

                $all_report[$all_counter] = $arr;
                $all_counter += 1;
            }
        }

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'data'    => $all_report
        ]);
    }
}
