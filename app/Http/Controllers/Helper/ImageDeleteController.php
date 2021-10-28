<?php

namespace App\Http\Controllers\Helper;

use App\Models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Cloudinary;

class ImageDeleteController extends Controller
{
    private $arr_table = [
        'banner' => Models\Banner::class,
        'classes' => Models\Classes::class,
        'users' => Models\User::class,
        'post_image' => Models\PostImage::class,
        'words' => Models\Words::class,
        'question-exam' => Models\Exam::class,
        'question-task' => Models\Task::class,
        'option' => Models\Option::class
    ];
    
    private $arr_banner_field = [
        'url_web',
        'web_id',
        'url_mobile',
        'mobile_id',
    ];

    private $arr_classes_field = [
        'url_web',
        'web_id',
        'url_mobile',
        'mobile_id',
    ];
    private $arr_users_field = [
        'url_foto',
        'foto_id',
    ];
    private $arr_post_image_field = [
        'url_gambar',
        'gambar_id',
    ];
    private $arr_words_field = [
        'url_pengucapan',
        'pengucapan_id',
    ];
    private $arr_question_field = [
        'url_gambar',
        'gambar_id',
        'url_file',
        'file_id',
    ];
    private $arr_option_field = [
        'url_gambar',
        'gambar_id',
        'url_file',
        'file_id',
    ];
    public function imageDelete(Request $request){
        if(!$position = $request->position || !$field = $request->field || !$uuid = $request->uuid){
            return response()->json([
                'message'=>'Failed',
                'info'=>"Token Tidak Sesuai"
            ]);
        }

        if(!$sel_table = $this->arr_table[$request->position]){
            return response()->json([
                'message'=>'Failed',
                'info'=>"Token Tidak Sesuai"
            ]);
        }
        if($request->position == 'banner'){

        }elseif($request->position == 'classes'){
            if(count($classes = Models\Classes::where('uuid',$request->uuid)->get())==0){
                return response()->json([
                    'message'=>'Failed',
                    'info'=>"ID Tidak Sesuai"
                ]);
            }
            if($request->field == 'web'){
                if($classes[0]->web_id != ''){
                    $this->deleteImage($classes[0]->web_id);
                }
                $classes = Models\Classes::where('uuid',$request->uuid)->update([
                    'url_web' => '',
                    'web_id' => '',
                ]);
            }elseif($request->field == 'mobile'){
                if($classes[0]->mobile_id != ''){
                    $this->deleteImage($classes[0]->mobile_id);
                }
                $classes = Models\Classes::where('uuid',$request->uuid)->update([
                    'url_mobile' => '',
                    'mobile_id' => '',
                ]);
            }

        }elseif($request->position == 'users'){

        }elseif($request->position == 'post_image'){

        }elseif($request->position == 'words'){

        }elseif($request->position == 'question-exam'){
            if(count($exam = Models\Exam::where('uuid',$request->uuid)->get())==0){
                return response()->json([
                    'message'=>'Failed',
                    'info'=>"ID Tidak Sesuai"
                ]);
            }
            if(count($question = Models\Question::where('id',$exam[0]->id_question)->get())==0){
                return response()->json([
                    'message'=>'Failed',
                    'info'=>"ID Tidak Sesuai"
                ]);
            }
            //return $question;
            if($request->field == 'image'){
                if($question[0]->gambar_id != ''){
                    $this->deleteImage($question[0]->gambar_id);
                }
                $question = Models\Question::where('uuid',$question[0]->uuid)->update([
                    'url_gambar' => '',
                    'gambar_id' => '',
                ]);
            }elseif($request->field == 'audio'){
                if($question[0]->file_id != ''){
                    $this->deleteFile($question[0]->file_id);
                }
                $question = Models\Question::where('uuid',$question[0]->uuid)->update([
                    'url_file' => '',
                    'file_id' => '',
                ]);
            }

        }elseif($request->position == 'question-task'){
            if(count($exam = Models\Task::where('uuid',$request->uuid)->get())==0){
                return response()->json([
                    'message'=>'Failed',
                    'info'=>"ID Tidak Sesuai"
                ]);
            }
            if(count($question = Models\Question::where('id',$exam[0]->id_question)->get())==0){
                return response()->json([
                    'message'=>'Failed',
                    'info'=>"ID Tidak Sesuai"
                ]);
            }
            //return $question;
            if($request->field == 'image'){
                if($question[0]->gambar_id != ''){
                    $this->deleteImage($question[0]->gambar_id);
                }
                $question = Models\Question::where('uuid',$question[0]->uuid)->update([
                    'url_gambar' => '',
                    'gambar_id' => '',
                ]);
            }elseif($request->field == 'audio'){
                if($question[0]->file_id != ''){
                    $this->deleteFile($question[0]->file_id);
                }
                $question = Models\Question::where('uuid',$question[0]->uuid)->update([
                    'url_file' => '',
                    'file_id' => '',
                ]);
            }

        }elseif($request->position == 'option'){

        }
        return response()->json([
            'message'=>'Success',
            'info'=>"Hapus Gambar Berhasil"
        ]);
    }

    public function deleteImage($getPublicId){
        Cloudinary::destroy($getPublicId);
    }

    public function deleteFile($getPublicId){
        Cloudinary::destroy($getPublicId, array("resource_type"=>"video"));
    }
}
