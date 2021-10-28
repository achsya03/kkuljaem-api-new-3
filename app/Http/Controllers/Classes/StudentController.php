<?php

namespace App\Http\Controllers\Classes;

use App\Models;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Helper;
use Illuminate\Http\Request;
use Validator;

class StudentController extends Controller
{
    public function __construct(Request $request){
        $this->middleware('auth');
    }

    public function studentVideoAdd(Request $request){
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

        

        $siswa = Models\Student::where('id_class',$video[0]->content->id_class)
                ->where('id_user',$request->user()->id)->get();

        


        if($video[0]->content->number == 1 || count($siswa)==0){
            # create student
            $validation = new Helper\ValidationController('student');
            $uuid1 = $validation->data['uuid'];

            $data = [
                'id_user' => $request->user()->id,
                'id_class' => $video[0]->content->id_class,
                'register_date' => date('Y/m/d'),
                'jml_pengerjaan' => 0,
                'uuid' => $uuid1
            ];

            $input = new Helper\InputController('student',$data);

            $siswa = Models\Student::where('uuid',$uuid1)->get();

            if(count($sisVid = Models\StudentVideo::where('id_student',$siswa[0]->id)
                ->where('id_video',$video[0]->id)->get())>0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Video Siswa Telah Terdaftar'
            ]);
        }
        }


        //return $siswa;
        # create student video
        $validation1 = new Helper\ValidationController('studentVideo');
        $uuid2 = $validation1->data['uuid'];

        $data = [
            'id_student' => $siswa[0]->id,
            'id_video' => $video[0]->id,
            'register_date' => date('Y/m/d'),
            'uuid' => $uuid2
        ];
        $input = new Helper\InputController('studentVideo',$data);

        # update student

        $data = [
            'jml_pengerjaan' => $siswa[0]->jml_pengerjaan+1,
            'uuid' => $siswa[0]->uuid
        ];
        $update = new Helper\UpdateController('student',$data);

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'info'    => 'Proses Input Siswa Video Berhasil'
        ]);
    }

    public function studentQuizAdd(Request $request){
        $result = [];
        if(!$uuid = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        
        $quiz = Models\Quiz::where('uuid',$uuid)->get();
        if(count($quiz)==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }

        

        $siswa = Models\Student::where('id_class',$quiz[0]->content->id_class)
                ->where('id_user',$request->user()->id)->get();


        $validation3 = new Helper\ValidationController('studentAnswer');
        $this->rules = $validation3->rules;
        $this->messages = $validation3->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        //$return_data=$validator->validated();
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        $validation1 = new Helper\ValidationController('studentQuiz');
        $this->rules = $validation1->rules;
        $this->messages = $validation1->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        //$return_data=$validator->validated();
        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        if($quiz[0]->content->number == 1 || count($siswa)==0){
            # create student
            $validation = new Helper\ValidationController('student');
            $uuid1 = $validation->data['uuid'];

            $data = [
                'id_user' => $request->user()->id,
                'id_class' => $quiz[0]->content->id_class,
                'register_date' => date('Y/m/d'),
                'jml_pengerjaan' => 0,
                'uuid' => $uuid1
            ];

            $input = new Helper\InputController('student',$data);

            $siswa = Models\Student::where('uuid',$uuid1)->get();


            if(count($sisVid = Models\StudentQuiz::where('id_student',$siswa[0]->id)
                    ->where('id_quiz',$quiz[0]->id)->get())>0){
                return response()->json([
                    'message' => 'Failed',
                    'error' => 'Quiz Siswa Telah Terdaftar'
                ]);
            }
        }


        //return $siswa;
        # create student quiz
        $uuid2 = $validation1->data['uuid'];

        $data = [
            'id_student' => $siswa[0]->id,
            'id_quiz' => $quiz[0]->id,
            'register_date' => date('Y/m/d'),
            'nilai' => $request->nilai,
            'uuid' => $uuid2
        ];
        $input = new Helper\InputController('studentQuiz',$data);

        # update student
        # create student quiz
        $quiz = Models\StudentQuiz::where('uuid',$uuid2)->first();
        
        $uuid3 = $validation3->data['uuid'];

        $data = [
            'id_student_quiz' => $quiz->id,
            'jawaban' => $request->jawaban,
            'uuid' => $uuid3
        ];
        $input = new Helper\InputController('studentAnswer',$data);


        $data = [
            'jml_pengerjaan' => $siswa[0]->jml_pengerjaan+1,
            'uuid' => $siswa[0]->uuid
        ];
        $update = new Helper\UpdateController('student',$data);

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'info'    => 'Proses Input Siswa Quiz Berhasil'
        ]);
    }
}
