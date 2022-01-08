<?php

namespace App\Http\Controllers\Classes;

use App\Models;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper;
use Validator;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function checkData(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        if(count($quiz = Models\Quiz::where('uuid',$uuid)->get())==0){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        $exam = Models\Exam::where('id_quiz',$quiz[0]->id)->get();
        $result['nomor_soal'] = count($exam)+1;
    
        return response()->json(['message'=>'Success','data'
        => $result]);
    }

    public function addData(Request $request)
    {
        $validation = new Helper\ValidationController('exam');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        $return_data=$validator->validated();
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        $validation1 = new Helper\ValidationController('question');
        $this->rules = $validation1->rules;
        $this->messages = $validation1->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        $return_data=$validator->validated();
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }
        
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        if(count($quiz = Models\Quiz::where('uuid',$uuid)->get())==0){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $uuid1 = $validation1->data['uuid'];

        $uploadedFileUrl1 = [
            'getSecurePath' => '',
            'getPublicId' => '',
        ];

        $uploadedFileUrl2 = $uploadedFileUrl1;
        $url_pertanyaan ='';

        if(isset($request->gambar_pertanyaan)){
            $uploadedFileUrl1 = $validation1->UUidCheck($request->gambar_pertanyaan,'Question/Gambar');
        }

        if(isset($request->url_pertanyaan)){
            $uploadedFileUrl2 = $validation1->UUidCheck($request->url_pertanyaan,'Question/Audio');
        }
        $data = [
            'pertanyaan_teks'             => $request->pertanyaan_teks,
            'url_gambar'                  => $uploadedFileUrl1['getSecurePath'],
            'gambar_id'                   => $uploadedFileUrl1['getPublicId'],
            'url_file'                    => $uploadedFileUrl2['getSecurePath'],
            'file_id'                     => $uploadedFileUrl2['getPublicId'],
            'jenis_jawaban'                  => request('jenis_jawaban'),
            'jawaban'                     => $request->jawaban,
            'uuid'                        => $uuid1
        ];

        $input = new Helper\InputController('question',$data);

        $question = Models\Question::where('uuid',$uuid1)->first();

        for($i=0;$i<4;$i++){

            $validation2 = new Helper\ValidationController('option');
            $uuid2 = $validation2->data['uuid'];

            $uploadedFileUrl1 = [
                'getSecurePath' => '',
                'getPublicId' => '',
            ];

            $uploadedFileUrl2 = $uploadedFileUrl1;
            $url_opsi = '';

            if($request->gambar_opsi != null){
                if(isset($request->gambar_opsi[$i])){
                    if($gambar1 = $request->gambar_opsi[$i]){
                        $uploadedFileUrl1 = $validation2->UUidCheck($gambar1,'Option/Gambar');
                    }
                }
            }

            if($request->url_opsi != null){
                if(isset($request->url_opsi[$i])){
                    if($gambar1 = $request->url_opsi[$i]){
                        $uploadedFileUrl2 = $validation2->UUidCheck($gambar1,'Option/Gambar');
                    }
                }
            }
            $jawaban_teks = "-";
            if($request->jawaban_teks != null){
                if(isset($request->jawaban_teks[$i])){
                    $jawaban_teks = $request->jawaban_teks[$i];
                }
            }

            if(isset($request->jawaban_teks[$i])){
                $jawaban_teks = $request->jawaban_teks[$i];
            }
            $opsi = ['A','B','C','D'];
            // if($gambar2 = $request->file_opsi[$i]){
            //     $uploadedFileUrl2 = $validation2->UUidCheck($gambar2,'Option/File');
            // }
            $data = [
                'id_question'                 => $question->id,
                'jawaban_teks'                => $jawaban_teks,
                'url_gambar'                  => $uploadedFileUrl1['getSecurePath'],
                'gambar_id'                   => $uploadedFileUrl1['getPublicId'],
                'url_file'                    => $uploadedFileUrl2['getSecurePath'],
                'file_id'                     => $uploadedFileUrl2['getPublicId'],
                'uuid'                        => $uuid2
            ];

            $input = new Helper\InputController('option',$data);
        }

        $uuid1 = $validation->data['uuid'];

        $data = [
            'id_question'               => $question->id,
            'id_quiz'                  => $quiz[0]->id,
            'number'                    => $request->nomor,
            'uuid'                      => $uuid1
        ];

        $input = new Helper\InputController('exam',$data);
        $data = [
            
            'jml_pertanyaan'            => $quiz[0]->jml_pertanyaan+1,
            'uuid'                      => $quiz[0]->uuid
        ];

        $update = new Helper\UpdateController('contentQuiz',$data);


        return response()->json(['message'=>'Success','info'
        => 'Proses Input Berhasil']);
    }
    
    public function detailData(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        if(count($exam = Models\Exam::where('uuid',$uuid)->get())==0){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $result['nomor'] = $exam[0]->number;
        $result['pertanyaan_teks'] = $exam[0]->question->pertanyaan_teks;
        if($exam[0]->question->url_gambar != null){$result['url_gambar'] = $exam[0]->question->url_gambar;}
        if($exam[0]->question->url_file != null){$result['url_file'] = $exam[0]->question->url_file;}
        $result['jenis_jawaban'] = $exam[0]->question->jenis_jawaban;
        $result['jawaban'] = $exam[0]->question->jawaban;
        $result['exam_uuid'] = $exam[0]->uuid;

        $arr = [];
        $option = Models\Option::where('id_question',$exam[0]->question->id)->orderBy('id','ASC')->get();
        for($i=0;$i<count($option);$i++){
            $arr1 = [];
            $opt = ['A','B','C','D'];

            $arr1['jawaban_id'] = $opt[$i];
            $arr1['jawaban_teks'] = $option[$i]->jawaban_teks;
            if($option[$i]->url_gambar != null){$arr1['url_gambar'] = $option[$i]->url_gambar;}
            if($option[$i]->url_file != null){$arr1['url_file'] = $option[$i]->url_file;}

            $arr[$i] = $arr1;
        }

        $result['pilihan'] = $arr;

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'data'    => $result
        ]);
    }

    public function updateData(Request $request)
    {
        $validation = new Helper\ValidationController('exam');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        $return_data=$validator->validated();
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        $validation1 = new Helper\ValidationController('question');
        $this->rules = $validation1->rules;
        $this->messages = $validation1->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        $return_data=$validator->validated();
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }
                
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        if(count($exam = Models\Exam::where('uuid',$uuid)->get())==0){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $uuid1 = $exam[0]->question->uuid;

        $uploadedFileUrl1 = [
            'getSecurePath' => '',
            'getPublicId' => '',
        ];

        $uploadedFileUrl2 = $uploadedFileUrl1;
        $url_pertanyaan ='';

        if(isset($request->gambar_pertanyaan)){
            if($exam[0]->question->gambar_id){
                $validation1->deleteImage($exam[0]->question->gambar_id);
            }
            $uploadedFileUrl1 = $validation1->UUidCheck($request->gambar_pertanyaan,'Question/Gambar');
        }
        if(isset($request->url_pertanyaan)){
            if($exam[0]->question->file_id){
                $validation1->deleteImage($exam[0]->question->file_id);
            }
            $uploadedFileUrl2 = $validation1->UUidCheck($request->url_pertanyaan,'Question/Audio');
        }

        //if($exam[0]->question->jenis_jawaban != request('jenis_jawaban')){
        //$delete = Models\Option::where('id_question',$exam[0]->question->id)->delete();
        //}

        $data = [
            'pertanyaan_teks'             => $request->pertanyaan_teks,
            'url_gambar'                  => $uploadedFileUrl1['getSecurePath'],
            'gambar_id'                   => $uploadedFileUrl1['getPublicId'],
            'url_file'                    => $uploadedFileUrl2['getSecurePath'],
            'file_id'                     => $uploadedFileUrl2['getPublicId'],
            'jenis_jawaban'                  => request('jenis_jawaban'),
            'jawaban'                     => $request->jawaban,
            'uuid'                        => $uuid1
        ];

        $input = new Helper\UpdateController('question',$data);

        $option = Models\Option::where('id_question',$exam[0]->question->id)->get();

        for($i=0;$i<4;$i++){

            $validation2 = new Helper\ValidationController('option');
            $uuid2 = $option[$i]->uuid;

            $uploadedFileUrl1 = [
                'getSecurePath' => '',
                'getPublicId' => '',
            ];

            $uploadedFileUrl2 = $uploadedFileUrl1;
            $url_opsi = '';

            if($request->gambar_opsi != null){
                if(isset($request->gambar_opsi[$i])){
                    if($gambar1 = $request->gambar_opsi[$i]){
                        $uploadedFileUrl1 = $validation2->UUidCheck($gambar1,'Option/Gambar');
                    }
                }
            }

            if($request->url_opsi != null){
                if(isset($request->url_opsi[$i])){
                    if($gambar1 = $request->url_opsi[$i]){
                        $uploadedFileUrl2 = $validation2->UUidCheck($gambar1,'Option/Gambar');
                    }
                }
            }
            $jawaban_teks = "-";
            if($request->jawaban_teks != null && request('jenis_jawaban')=='Teks'){
                if(isset($request->jawaban_teks[$i])){
                    $jawaban_teks = $request->jawaban_teks[$i];
                }
            }
            $opsi = ['A','B','C','D'];
            // if($gambar2 = $request->file_opsi[$i]){
            //     $uploadedFileUrl2 = $validation2->UUidCheck($gambar2,'Option/File');
            // }
            $data = [
                'id_question'                 => $exam[0]->question->id,
                'jawaban_teks'                => $jawaban_teks,
                'url_gambar'                  => $uploadedFileUrl1['getSecurePath'],
                'gambar_id'                   => $uploadedFileUrl1['getPublicId'],
                'url_file'                    => $uploadedFileUrl2['getSecurePath'],
                'file_id'                     => $uploadedFileUrl2['getPublicId'],
                'uuid'                        => $uuid2
            ];

            $input = new Helper\UpdateController('option',$data);
        }
        $result['quiz_uuid'] = $exam[0]->quiz->uuid;

        return response()->json([
            'message'=>'Success',
            'info'=> 'Proses Update Berhasil',
            'data'=> $result
        ]);
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
        
        $exam = Models\Exam::where('uuid',$uuid)->get();
        if(count($exam)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        #update video
        $quiz = Models\Quiz::where('id',$exam[0]->id_quiz)->update([
            'jml_pertanyaan' => $exam[0]->quiz->jml_pertanyaan-1
        ]);

        #delete question
        $delete = Models\Question::where('id',$exam[0]->id_question)->delete();

        #delete exam
        $delete = Models\Exam::where('uuid',$uuid)->delete();
        
        #Update Number
        $exams = Models\Exam::where('id_quiz',$exam[0]->id_quiz)
                    ->orderBy('number','ASC')->get();
        if(count($exams)>0){
            for($i=0;$i<count($exams);$i++){
                Models\Exam::where('id',$exams[$i]->id)->update([
                    'number' => $i+1
                ]);
            }
        }

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'info'    => 'Proses Hapus Content Quiz Berhasil'
        ]);
    }
}
