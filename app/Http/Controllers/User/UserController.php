<?php

namespace App\Http\Controllers\User;

use App\Models;
use App\Models\User;
use App\Models\DetailMentor;
use App\Models\DetailStudent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\DetailMentorController;
use App\Http\Controllers\Auth\DetailStudentController;
use App\Http\Controllers\MailController;
use Illuminate\Http\Request;
use Validator;
use Hash;
use Session;
use Cloudinary;
use Illuminate\Support\Str;
use App\Http\Controllers\Helper;
use DateTime;

class UserController extends Controller
{
    private function statUser($user){
        $stUsr = "Non-Member";
        $jenis_akun=['No Sign','Helm','Crown Silver'];
        if(date_format(date_create($user->tgl_langganan_akhir),"Y/m/d") >= date('Y/m/d')){
            $stUsr = "Member";
        }
        if($user->url_foto!=null || $user->url_foto!=''){

        }
        //$data['email'] = $user->email;
        if($user->jenis_pengguna!='0'){
            if($user->url_foto!=null || $user->url_foto!=''){$data['foto'] = $user->url_foto;}
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
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    private $rules = [
        'email'                 => 'required|email|unique:users,email',
        'password'              => 'required|confirmed',
        'password_confirmation' => 'required',
        #'jenis_pengguna'        => 'required' # 0 Siswa, 1 Mentor, 2 Admin];
    ];

    private $messages = [
        'email.required'                  => 'Email wajib diisi',
        'email.email'                     => 'Email tidak valid',
        'email.unique'                    => 'Email sudah terdaftar',
        'password.required'               => 'Password wajib diisi',
        'password.confirmed'              => 'Password tidak sama dengan konfirmasi password',
        'password_confirmation.required'  => 'Konfirmasi password wajib diisi',
        #'jenis_pengguna.required'         => 'Jenis Penggguna wajib diisi'
    ];

    public static function randomToken($number){
        $web_token = Str::random($number);

        while(count(User::where('web_token',$web_token)->get())>0){
            $web_token = Str::random(144);
            #return response(User::where('web_token',"177Z2jb4RfdgDYAGp04lDBuqPLFeseGb")->get());
        }
        return $web_token;
    }

    public function checkData(Request $request)
    {
        if($request->user()==NULL){
            return response("You are Logout", 205);
        }
        return $request->user()->nama;
    }

    private function getUuid(){
        $uuid = (string) str_replace('-','',Str::uuid());

        $uuid_exist = count(User::where('uuid',$uuid)->get());
        while ($uuid_exist > 0) {
            $uuid = (string) str_replace('-','',Str::uuid());
            $uuid_exist = count(User::where('uuid',$uuid)->get());
        }

        return $uuid;
    }


    private function UUidCheck($gambar){
        if(count($gambar)>1){
            return response()->json(['message'=>"Failed",'info'=>"Hanya  bisa memilih satu gambar"],401);
        }

        if(!$uploadedFileUrl = Cloudinary::uploadFile($gambar[0]->getRealPath(),[
            'folder' => "/Profile",
            'use_filename' => 'True',
            'filename_override' => date('mdYhis')
        ])){
            return response()->json(['message'=>'Failed','info'=>"Proses Gagal"]);
        }
        $uploadResponse = [
            'getSecurePath'   =>  $uploadedFileUrl->getSecurePath(),
            'getPublicId'     =>  $uploadedFileUrl->getPublicId()
        ];

        return $uploadResponse;
    }

    private function addDataMentor(){
        $this->rules += [
            'nama'                  => 'required',
            'bio'                   => 'required',
            'awal_mengajar'         => 'required',
            'url_foto'              => 'required',
            'jenis_pengguna'              => 'required'
        ];
    
        $this->messages += [
            'nama.required'                   => 'Nama wajib diisi',
            'bio.required'                    => 'Bio wajib diisi',
            'awal_mengajar.required'          => 'Konfirmasi password wajib diisi',
            'url_foto.required'               => 'Foto wajib diisi',
            'jenis_pengguna.required'               => 'Jenis Pengguna wajib diisi'
        ];
    }

    private function addDataStudent(){
        $this->rules += [
            'nama'                  => 'required',
            'alamat'                  => 'required',
            'jenis_kel'               => 'required',
            'tgl_lahir'               => 'required',
            'jenis_pengguna'              => 'required'
        ];
    
        $this->messages += [
            'nama.required'                   => 'Nama wajib diisi',
            'bio.required'                    => 'Bio wajib diisi',
            'jenis_kel.required'              => 'Jenis Kelamin password wajib diisi',
            'tgl_lahir.required'              => 'Tanggal Lahir wajib diisi',
            'jenis_pengguna.required'               => 'Jenis Pengguna wajib diisi'
        ];

    }

    private function jenisPenggunaCheck($jenis_pengguna){
        $arr = ['student','mentor','admin'];
        if(!$result = in_array($jenis_pengguna,$arr)){
            return response()->json(['message'=>"Jenis Pengguna Tidak Valid"]);
        }
        $id_pengguna = array_search($jenis_pengguna, $arr);
        if($id_pengguna==0){
            $this->addDataStudent();
        }elseif($id_pengguna==1 || $id_pengguna==2){
            $this->addDataMentor();
        }
    }

    public function studentList(Request $request){
        $student1 = User::where('jenis_pengguna',0)->get();
        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 2000;
        $counter_student = count($student1);
        $max_page = ceil($counter_student / $limit);

        if($page > $max_page){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Max page tidak sesuai'
            ]);
        }

        $student = User::where('jenis_pengguna',0)
                ->orderBy('nama','ASC')
                ->limit($limit)->offset(($page - 1) * $limit)->get();

        $arr = [];
        for($i=0;$i<count($student);$i++){
            $status = 'Belum Terverifikasi';
            if($student[$i]->email_verified_at != null || $student[$i]->email_verified_at != '' ){
                $status = 'Terverifikasi';
            }
            if(date_format(date_create($student[$i]->tgl_langganan_akhir),"Y/m/d") >= date('Y/m/d')){
                $status = 'Member';
            }
            $jenis_kel = '-';
            $tgl_lahir = '-';
            $alamat = '-';
            $tempat_lahir = '-';
            if(count($student[$i]->detailStudent)>0){
                if($student[$i]->detailStudent[0]->jenis_kel!=null){$jenis_kel = $student[$i]->detailStudent[0]->jenis_kel;}
                if($student[$i]->detailStudent[0]->tgl_lahir!=null){$tgl_lahir = $student[$i]->detailStudent[0]->tgl_lahir;}
                if($student[$i]->detailStudent[0]->alamat!=null){$alamat = $student[$i]->detailStudent[0]->alamat;}
                if($student[$i]->detailStudent[0]->tempat_lahir!=null){$tempat_lahir = $student[$i]->detailStudent[0]->tempat_lahir;}
            }

            $arr1 = [
                'status'=>$status,
                'email'=>$student[$i]->email,
                'nama'=>$student[$i]->nama,
                'jenis_kel'=>$jenis_kel,
                'tgl_lahir'=>$tgl_lahir,
                'tempat_lahir'=>$tempat_lahir,
                'alamat'=>$alamat,
                'user_uuid'=>$student[$i]->uuid,
            ];
            $arr[$i] = $arr1;
        }

        return response()->json([
            'message' => 'Success',
            'max_page' => $max_page,
            //'account' => $this->statUser($request->user()),
            'data'    => $arr
        ]);
    }

    public function mentorList(Request $request){
        $mentor = User::where('jenis_pengguna','!=',0)->get();

        $arr = [];
        for($i=0;$i<count($mentor);$i++){
            $status = 'Belum Terverifikasi';
            $usr = '';
            if($mentor[$i]->email_verified_at != null || $mentor[$i]->email_verified_at != '' ){
                $status = 'Terverifikasi';
            }
            if(date_format(date_create($mentor[$i]->tgl_langganan_akhir),"Y/m/d") >= date('Y/m/d')){
                $status = 'Member';
            }

            if($mentor[$i]->jenis_pengguna == 1 ){
                $usr = 'Mentor';
            }elseif($mentor[$i]->jenis_pengguna == 2 ){
                $usr = 'Mentor/Admin';
            }

            $bio = '-';
            if(count($mentor[$i]->detailMentor)>0){
                $bio = $mentor[$i]->detailMentor[0]->bio;
            }
            $arr1 = [
                'status'=>$status,
                'email'=>$mentor[$i]->email,
                'nama'=>$mentor[$i]->nama,
                'jenis_pengguna'=>$usr,
                'bio'=>$bio,
                'user_uuid'=>$mentor[$i]->uuid,
            ];
            $arr[$i] = $arr1;
        }

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'data'    => $arr
        ]);
    }

    public function addData(Request $request){
        $jenis_pengguna=$request->jenis_pengguna;
        //$this->jenisPenggunaCheck($jenis_pengguna);
        
        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;
        if($validator->fails()){
            return response()->json(['message'=>$validator->errors(),'info'=>$validator->errors()]);
        }
        if($request->jenis_pengguna != 'student' and $request->jenis_pengguna != 'mentor' and $request->jenis_pengguna != 'admin'){
            return response()->json(['message'
            => 'Jenis Pengguna Tidak Terdaftar'],401);
        }


        $arr = ['student','mentor','admin'];
        $id_pengguna = array_search($jenis_pengguna, $arr);
        $id_pengguna = -1;
        if($jenis_pengguna == 'student'){
            $id_pengguna = 0;
        }elseif($jenis_pengguna == 'mentor'){
            $id_pengguna = 1;
        }elseif($jenis_pengguna == 'admin'){
            $id_pengguna = 2;
        }
        //return $id_pengguna.'-'.$jenis_pengguna;

        $web_token = $this->randomToken(144);

        $info_pengguna=[
            #'nama' => request('nama'),
            'email' => request('email'),
            'web_token' => $web_token,
        ];

        if(!$kirim_email=MailController::sendEmail($info_pengguna,"verify")){
            return response()->json(['message'
            => 'Email Not Send'],401);
        }
        $uuid=$this->getUuid();


        User::create([
            'nama' => request('nama'),
            'email' => request('email'),
            'password' => bcrypt(request('password')),
            'web_token' => $web_token,
            'jenis_pengguna' => $id_pengguna,
            'jenis_akun' => 2,
            'uuid' => $uuid
        ]);

        $id_user = User::where('uuid', $uuid)->first()->id;
        
        if($id_pengguna==1 or $id_pengguna==2){
            $uploadedFileUrl1 = [
                'getSecurePath'=>'',
                'getPublicId'=>'',
            ];

            if(isset($request->url_foto)){
                $gambar1 = $request->url_foto;
                $uploadedFileUrl1 = $this->UUidCheck($gambar1);
            }
            
            $uuid1 = DetailMentorController::getUuid();
            $awal_mengajar = date_format(date_create($request->awal_mengajar),"Y/m/d");
            $data_user=[
                'id_users'  => $id_user,
                'bio'       => $request->bio,
                'awal_mengajar' => $awal_mengajar,
                'url_foto' => $uploadedFileUrl1['getSecurePath'],
                'foto_id' => $uploadedFileUrl1['getPublicId'],
                'uuid' => $uuid1
            ];
            DetailMentorController::addData($data_user);
        }elseif($id_pengguna==0){
            $uuid1 = DetailStudentController::getUuid();
            $tgl_lahir = date_format(date_create($request->tgl_lahir),"Y/m/d");
            $data_user=[
                'id_users'  => $id_user,
                'alamat'       => $request->alamat,
                'jenis_kel' => $request->jenis_kel,
                'tgl_lahir' => $tgl_lahir,
                'tempat_lahir' => $request->tempat_lahir,
                'uuid' => $uuid1
            ];
            DetailStudentController::addData($data_user);
        }
        
        return response()->json(['message'
        => 'Success','info'=>'Silahkan Aktivasi Akun melalui email'],200);
    }

    public function detailData(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        if(($user = Models\User::where('uuid',$uuid)->first())==null){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        
        if($user->jenis_pengguna == 0){
            $status = 'Belum Terverifikasi';
            if($user->email_verified_at != null || $user->email_verified_at != '' ){
                $status = 'Terverifikasi';
            }
            if(date_format(date_create($user->tgl_langganan_akhir),"Y/m/d") >= date('Y/m/d')){
                $status = 'Member';
            }
            $jenis_kel = '-';
            $tgl_lahir = '-';
            $alamat = '-';
            $tempat_lahir = '-';
            if(count($user->detailStudent)>0){
                $jenis_kel = $user->detailStudent[0]->jenis_kel;
                $tgl_lahir = $user->detailStudent[0]->tgl_lahir;
                $alamat = $user->detailStudent[0]->alamat;
                $tempat_lahir = $user->detailStudent[0]->tempat_lahir;
            }
            //date_format(date_create($user->tgl_langganan_akhir),"Y/m/d")->diff(date('Y/m/d'))
            $tglAkhir = 'Non-Member';
            if(date_format(date_create($user->tgl_langganan_akhir),"Y/m/d") >= date('Y/m/d')){
                $dt1 = new DateTime($user->tgl_langganan_akhir);
                $dt2 = new DateTime(date('Y/m/d'));
                $interval = $dt1->diff($dt2);
                $tglAkhir = "Sisa " . $interval->y . " tahun, " . $interval->m." bulan, ".$interval->d." hari "; 
            }
            $stat = ['Aktif','Non-Aktif'];

            $arr1 = [
                'status'=>$status,
                'email'=>$user->email,
                'tgl_akhir_member'=>$tglAkhir,
                'nama'=>$user->nama,
                'jenis_kel'=>$jenis_kel,
                'tgl_lahir'=>$tgl_lahir,
                'tempat_lahir'=>$tempat_lahir,
                'alamat'=>$alamat,
                'status_aktif'=>$user->status_aktif,
                'user_uuid'=>$user->uuid,
            ];

            $arr2 = [];
            for($i=0;$i<count($user->student);$i++){
                $classes = Models\Classes::where('id',$user->student[$i]->id_class)->first();
                $total = ($classes->jml_video+$classes->jml_kuis);
                if($total==0){
                    $total = 1;
                }
                $arr022 = [
                    'class_name' => $classes->nama,
                    'class_prosentase' => ($user->student[$i]->jml_pengerjaan / $total) * 100,
                    'class_uuid' => $classes->uuid,
                ];
                $arr2[$i] = $arr022;
            }
            $arr3 = [];
            for($i=0;$i<count($user->subs);$i++){
                $packet = Models\Packet::select(['lama_paket','harga'])->where('id',$user->subs[$i]->id_packet)->first();
                $reference = Models\Reference::select(['nama','kode'])->where('id',$user->subs[$i]->id_reference)->first();
                
                $status = 'GAGAL';
                if($user->subs[$i]->subs_status=='PENDING'){
                    $status = 'TUNGGU';
                }elseif($user->subs[$i]->subs_status=='PAID'){
                    $status = 'BERHASIL';
                }elseif($user->subs[$i]->subs_status=='UNPAID'){
                    $status = 'TUNGGU';
                }if(date_format(date_create($user->subs[$i]->tgl_akhir_bayar),"Y/m/d H:i:s") < date('Y/m/d H:i:s')){
                    $status = 'GAGAL';
                }
                $arr022 = [
                    'packet' => $packet,
                    'tgl_subs' => $user->subs[$i]->tgl_subs,
                    'reference' => $reference,
                    'subs_status' => $status,
                    'subs_uuid' => $user->subs[$i]->uuid,
                ];
                $arr3[$i] = $arr022;
            }

            $arr['user'] = $arr1;
            $arr['classes'] = $arr2;
            $arr['subscription'] = $arr3;
        }elseif($user->jenis_pengguna == 1 || $user->jenis_pengguna == 2){
            
            $status = 'Belum Terverifikasi';
            $usr = '';
            if($user->email_verified_at != null || $user->email_verified_at != '' ){
                $status = 'Terverifikasi';
            }
            if(date_format(date_create($user->tgl_langganan_akhir),"Y/m/d") >= date('Y/m/d')){
                $status = 'Member';
            }

            if($user->jenis_pengguna == 1 ){
                $usr = 'Mentor';
            }elseif($user->jenis_pengguna == 2 ){
                $usr = 'Mentor/Admin';
            }

            $bio = '-';
            if(count($user->detailMentor)>0){
                $bio = $user->detailMentor[0]->bio;
            }
            $arr1 = [
                'status'=>$status,
                'email'=>$user->email,
                'nama'=>$user->nama,
                'jenis_pengguna'=>$usr,
                'bio'=>$bio,
                'user_uuid'=>$user->uuid,
            ];

            $teacher = Models\Teacher::where('id_user',$user->id)->get();
            $arr2 = [];
            for($i=0;$i<count($teacher);$i++){
                $arr22 = [
                    'kelas_nama'=>$teacher[$i]->classes->nama,
                    'kelas_uuid'=>$teacher[$i]->classes->uuid
                ];
                $arr2[$i] = $arr22;
            }

            $arr['user'] = $arr1;
            $arr['classes'] = $arr2;
        }else{
            return response()->json(['message'=>'Failed','info'=>"Token dan Jenis Pengguna Tidak Sesuai"]);
        }

        return response()->json([
            'message' => 'Success',
            'account' => $this->statUser($request->user()),
            'data'    => $arr
        ]);
    }

    public function detailUserDataUpdateMentor(Request $request){
        if(!$uuid=$request->user()->uuid){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        if(($user = Models\User::where('uuid',$uuid)->first())==null){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        
        $arr = [];
        if($user->jenis_pengguna == 0){
            $status = 'Belum Terverifikasi';
            if($user->email_verified_at != null || $user->email_verified_at != '' ){
                $status = 'Terverifikasi';
            }
            if(date_format(date_create($user->tgl_langganan_akhir),"Y/m/d") >= date('Y/m/d')){
                $status = 'Member';
            }
            $jenis_kel = '-';
            $tgl_lahir = '-';
            $alamat = '-';
            $tempat_lahir = '-';
            if(count($user->detailStudent)>0){
                if($user->detailStudent[0]->jenis_kel!=''){$jenis_kel = $user->detailStudent[0]->jenis_kel;}
                if($user->detailStudent[0]->tgl_lahir!=''){$tgl_lahir = $user->detailStudent[0]->tgl_lahir;}
                if($user->detailStudent[0]->alamat!=''){$alamat = $user->detailStudent[0]->alamat;}
                if($user->detailStudent[0]->tempat_lahir!=''){$tempat_lahir = $user->detailStudent[0]->tempat_lahir;}
            }

            $arr1 = [
                'status'=>$status,
                'email'=>$user->email,
                'nama'=>$user->nama,
                'jenis_kel'=>$jenis_kel,
                'tgl_lahir'=>$tgl_lahir,
                'tempat_lahir'=>$tempat_lahir,
                'alamat'=>$alamat,
                'user_uuid'=>$user->uuid,
            ];

            $arr['user'] = $arr1;
        }elseif($user->jenis_pengguna == 1 || $user->jenis_pengguna == 2){
            
            $status = 'Belum Terverifikasi';
            $usr = '';
            if($user->email_verified_at != null || $user->email_verified_at != '' ){
                $status = 'Terverifikasi';
            }
            if(date_format(date_create($user->tgl_langganan_akhir),"Y/m/d") >= date('Y/m/d')){
                $status = 'Member';
            }

            if($user->jenis_pengguna == 1 ){
                $usr = 'Mentor';
            }elseif($user->jenis_pengguna == 2 ){
                $usr = 'Mentor/Admin';
            }

            $bio = '-';
            if(count($user->detailMentor)>0){
                if($user->detailMentor[0]->bio!=''){$bio = $user->detailMentor[0]->bio;}
            }
            $arr1 = [
                'status'=>$status,
                'email'=>$user->email,
                'nama'=>$user->nama,
                'foto'=>$user->url_foto,
                'jenis_pengguna'=>$usr,
                'bio'=>$bio,
                'user_uuid'=>$user->uuid,
            ];

            $arr['user'] = $arr1;
        }

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'data'    => $arr
        ]);
    }

    public function detailUserDataUpdate(Request $request){
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        if(($user = Models\User::where('uuid',$uuid)->first())==null){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        
        $arr = [];
        if($user->jenis_pengguna == 0){
            $status = 'Belum Terverifikasi';
            if($user->email_verified_at != null || $user->email_verified_at != '' ){
                $status = 'Terverifikasi';
            }
            if(date_format(date_create($user->tgl_langganan_akhir),"Y/m/d") >= date('Y/m/d')){
                $status = 'Member';
            }
            $jenis_kel = '-';
            $tgl_lahir = '-';
            $alamat = '-';
            $tempat_lahir = '-';
            if(count($user->detailStudent)>0){
                if($user->detailStudent[0]->jenis_kel!=''){$jenis_kel = $user->detailStudent[0]->jenis_kel;}
                if($user->detailStudent[0]->tgl_lahir!=''){$tgl_lahir = $user->detailStudent[0]->tgl_lahir;}
                if($user->detailStudent[0]->alamat!=''){$alamat = $user->detailStudent[0]->alamat;}
                if($user->detailStudent[0]->tempat_lahir!=''){$tempat_lahir = $user->detailStudent[0]->tempat_lahir;}
            }

            $arr1 = [
                'status'=>$status,
                'email'=>$user->email,
                'nama'=>$user->nama,
                'jenis_kel'=>$jenis_kel,
                'tgl_lahir'=>$tgl_lahir,
                'tempat_lahir'=>$tempat_lahir,
                'alamat'=>$alamat,
                'user_uuid'=>$user->uuid,
            ];

            $arr['user'] = $arr1;
        }elseif($user->jenis_pengguna == 1 || $user->jenis_pengguna == 2){
            
            $status = 'Belum Terverifikasi';
            $usr = '';
            if($user->email_verified_at != null || $user->email_verified_at != '' ){
                $status = 'Terverifikasi';
            }
            if(date_format(date_create($user->tgl_langganan_akhir),"Y/m/d") >= date('Y/m/d')){
                $status = 'Member';
            }

            if($user->jenis_pengguna == 1 ){
                $usr = 'Mentor';
            }elseif($user->jenis_pengguna == 2 ){
                $usr = 'Mentor/Admin';
            }

            $bio = '-';
            if(count($user->detailMentor)>0){
                if($user->detailMentor[0]->bio!=''){$bio = $user->detailMentor[0]->bio;}
            }
            $arr1 = [
                'status'=>$status,
                'email'=>$user->email,
                'nama'=>$user->nama,
                'foto'=>$user->url_foto,
                'jenis_pengguna'=>$usr,
                'awal_mengajar'=>$user->detailMentor[0]->awal_mengajar,
                'bio'=>$bio,
                'user_uuid'=>$user->uuid,
            ];

            $arr['user'] = $arr1;
        }

        return response()->json([
            'message' => 'Success',
            //'account' => $this->statUser($request->user()),
            'data'    => $arr
        ]);
    }

    public function detailUserData(Request $request){
        $user = $request->user();
        
        if($user->jenis_pengguna == 0){
            $status = 'Belum Terverifikasi';
            if($user->email_verified_at != null || $user->email_verified_at != '' ){
                $status = 'Terverifikasi';
            }
            if(date_format(date_create($user->tgl_langganan_akhir),"Y/m/d") >= date('Y/m/d')){
                $status = 'Member';
            }
            $jenis_kel = '-';
            $tgl_lahir = '-';
            $alamat = '-';
            $tempat_lahir = '-';
            if(count($user->detailStudent)>0){
                $jenis_kel = $user->detailStudent[0]->jenis_kel;
                $tgl_lahir = $user->detailStudent[0]->tgl_lahir;
                $alamat = $user->detailStudent[0]->alamat;
                $tempat_lahir = $user->detailStudent[0]->tempat_lahir;
            }

            $arr1 = [
                'status'=>$status,
                'email'=>$user->email,
                'nama'=>$user->nama,
                'jenis_kel'=>$jenis_kel,
                'tgl_lahir'=>$tgl_lahir,
                'tempat_lahir'=>$tempat_lahir,
                'alamat'=>$alamat,
                'user_uuid'=>$user->uuid,
            ];

            $arr['user'] = $arr1;
        }elseif($user->jenis_pengguna == 1 || $user->jenis_pengguna == 2){
            
            $status = 'Belum Terverifikasi';
            $usr = '';
            if($user->email_verified_at != null || $user->email_verified_at != '' ){
                $status = 'Terverifikasi';
            }
            if(date_format(date_create($user->tgl_langganan_akhir),"Y/m/d") >= date('Y/m/d')){
                $status = 'Member';
            }

            if($user->jenis_pengguna == 1 ){
                $usr = 'Mentor';
            }elseif($user->jenis_pengguna == 2 ){
                $usr = 'Mentor/Admin';
            }

            $bio = '-';
            if(count($user->detailMentor)>0){
                $bio = $user->detailMentor[0]->bio;
            }
            $arr1 = [
                'status'=>$status,
                'email'=>$user->email,
                'nama'=>$user->nama,
                'foto'=>$user->url_foto,
                'jenis_pengguna'=>$usr,
                'bio'=>$bio,
                'user_uuid'=>$user->uuid,
            ];

        }

        return response()->json([
            'message' => 'Success',
            'account' => $this->statUser($request->user()),
            'data'    => $arr
        ]);
    }

    public function updateDataStudent(Request $request)
    {
        $validation = new Helper\ValidationController('userStudent');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;

        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }
        
        $user = Models\User::where('uuid',$request->user()->uuid)
                ->update([
                    'nama' => request('nama'),
                    'jenis_akun' => '2'
                ]);

        if(count($request->user()->detailStudent)>0){
            $detailStudent = Models\DetailStudent::where('id_users',$request->user()->id)
                ->update([
                    'alamat'             => request('alamat'),
                    'jenis_kel'      => request('jenis_kel'),
                    'tgl_lahir'      => request('tgl_lahir'),
                    'tempat_lahir'      => request('tempat_lahir'),
                ]);
        }elseif(count($request->user()->detailStudent)==0){
            $uuid = $validation->data['uuid'];
            $detailStudent = Models\DetailStudent::create([
                    'id_users'             => $request->user()->id,
                    'alamat'             => request('alamat'),
                    'jenis_kel'      => request('jenis_kel'),
                    'tgl_lahir'      => request('tgl_lahir'),
                    'tempat_lahir'      => request('tempat_lahir'),
                    'uuid'      => $uuid
                ]);
        }
        
        return response()->json(['message'=>'Success','info'
        => 'Proses Update Berhasil']);
    }

    public function updateDataStudents(Request $request)
    {
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        if(($user = Models\User::where('uuid',$uuid)
                    ->where('jenis_pengguna','0')->first())==null){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        if(count($user2 = Models\User::where('uuid','!=',$uuid)
                    ->where('email',request('email'))->get())>0){
            return response()->json(['message'=>'Failed','info'=>"Email Telah Digunakan"]);
        }

        $validation = new Helper\ValidationController('userStudents');
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;

        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        $user_upd = Models\User::where('uuid',$user->uuid)
                ->update([
                    'email' => request('email'),
                    'nama' => request('nama'),
                    'password' => bcrypt(request('password')),
                    'jenis_akun' => request('jenis_akun'),
                ]);
        $det_student = Models\DetailStudent::where('id_users',$user->id)->get();

        if(count($det_student)>0){
            $detailStudent = Models\DetailStudent::where('id_users',$user->id)
                ->update([
                   // 'bio'             => request('bio'),
                    'jenis_kel'      => request('jenis_kel'),
                    'tgl_lahir'      => request('tgl_lahir'),
                    'alamat'      => request('alamat'),
                    'tempat_lahir'      => request('tempat_lahir'),
                ]);
        }elseif(count($det_student)==0){
            $uuid = $validation->data['uuid'];
            $detailStudent = Models\DetailStudent::create([
                    'id_users'             => $user->id,
                    //'bio'             => request('bio'),
                    'jenis_kel'      => request('jenis_kel'),
                    'tgl_lahir'      => request('tgl_lahir'),
                    'tempat_lahir'      => request('tempat_lahir'),
                    'alamat'      => request('alamat'),
                    'uuid'      => $uuid
                ]);
        }
        
        return response()->json(['message'=>'Success','info'
        => 'Proses Update Berhasil']);
    }

    public function updateDataStudentsStatus(Request $request)
    {
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        if(($user = Models\User::where('uuid',$uuid)
                    ->where('jenis_pengguna','0')->first())==null){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $status = '0';
        if($user->status_aktif=='0'){
            $status = '1';
        }
       
        $user_upd = Models\User::where('uuid',$user->uuid)
                ->update([
                    'status_aktif' => $status,
                ]);
        $det_student = Models\DetailStudent::where('id_users',$user->id)->get();

        return response()->json(['message'=>'Success','info'
        => 'Proses Update Berhasil']);
    }

    public function updateDeviceID(Request $request)
    {
        if(!$uuid=$request->device_id){
            return response()->json(['message'=>'Failed','info'=>"Device ID Tidak Sesuai"]);
        }

       
        $user_upd = Models\User::where('uuid',$request->user()->uuid)
                ->update([
                    'device_id' => $request->device_id,
                ]);

        return response()->json(['message'=>'Success','info'
        => 'Proses Update Device ID Berhasil']);
    }

    public function updateDataMentors(Request $request)
    {
        if(!$uuid=$request->user()->uuid){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        if(($user = Models\User::where('uuid',$uuid)
                    ->whereIn('jenis_pengguna',[1,2])->first())==null){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        if(count($user2 = Models\User::where('uuid','!=',$uuid)
                    ->where('email',request('email'))->get())>0){
            return response()->json(['message'=>'Failed','info'=>"Email Telah Digunakan"]);
        }

        $validation = new Helper\ValidationController('userMentors1');
        if($request->password!=null || $request->password_confirmation!=null){
            $validation = new Helper\ValidationController('userMentors');
        }
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;

        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        $user_upd = Models\User::where('uuid',$user->uuid)
                ->update([
                    'email' => request('email'),
                    'nama' => request('nama'),
                    'jenis_akun' => '2'
                ]);

        if(isset($request->url_foto)){
            $gambar1 = $request->url_foto;
            $uploadedFileUrl1 = $validation->UUidCheck($gambar1,'User/Forum');
            $detailStudent = Models\User::where('id',$user->id)
            ->update([
                'url_foto'    => $uploadedFileUrl1['getSecurePath'],
                'foto_id'     => $uploadedFileUrl1['getPublicId'],
            ]);
        }
        if(isset($request->password)){
            if($request->password!=''){
                $detailStudent = Models\User::where('id',$user->id)
                ->update([
                    'password'    => bcrypt(request('password')),
                ]);
            }
        }
        $det_mentor = Models\DetailMentor::where('id_users',$user->id)->get();

        if(count($det_mentor)>0){
            $detailStudent = Models\DetailMentor::where('id_users',$user->id)
                ->update([
                    'bio'             => request('bio'),
                ]);
        }elseif(count($det_mentor)==0){
            $uuid = $validation->data['uuid'];
            Models\DetailMentor::create([
                'id_users'             => $user->id,
                    'bio'             => request('bio'),
                    'awal_mengajar'             => date('Y-m-d'),
                    'uuid'      => $uuid
                ]);
        }
        
        return response()->json(['message'=>'Success','info'
        => 'Proses Update Berhasil']);
    }

    public function updateDataMentor(Request $request)
    {
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        if(($user = Models\User::where('uuid',$uuid)
                    ->whereIn('jenis_pengguna',[1,2])->first())==null){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }
        if(count($user2 = Models\User::where('uuid','!=',$uuid)
                    ->where('email',request('email'))->get())>0){
            return response()->json(['message'=>'Failed','info'=>"Email Telah Digunakan"]);
        }

        $validation = new Helper\ValidationController('userMentors1');
        if($request->password!=null || $request->password_confirmation!=null){
            $validation = new Helper\ValidationController('userMentors');
        }
        $this->rules = $validation->rules;
        $this->messages = $validation->messages;

        $validator = Validator::make($request->all(), $this->rules, $this->messages);
        #echo $web_token;

        if($validator->fails()){
            return response()->json(['message'=>'Failed','info'=>$validator->errors()]);
        }

        $user_upd = Models\User::where('uuid',$user->uuid)
                ->update([
                    'email' => request('email'),
                    'nama' => request('nama'),
                    'jenis_akun' => '2'
                ]);

        if(isset($request->url_foto)){
            $gambar1 = $request->url_foto;
            $uploadedFileUrl1 = $validation->UUidCheck($gambar1,'User/Forum');
            $detailStudent = Models\User::where('id',$user->id)
            ->update([
                'url_foto'    => $uploadedFileUrl1['getSecurePath'],
                'foto_id'     => $uploadedFileUrl1['getPublicId'],
            ]);
        }
        if(isset($request->password)){
            if($request->password!=''){
                $detailStudent = Models\User::where('id',$user->id)
                ->update([
                    'password'    => bcrypt(request('password')),
                ]);
            }
        }
        $det_mentor = Models\DetailMentor::where('id_users',$user->id)->get();

        if(count($det_mentor)>0){
            $detailStudent = Models\DetailMentor::where('id_users',$user->id)
                ->update([
                    'bio'             => request('bio'),
                ]);
        }elseif(count($det_mentor)==0){
            $uuid = $validation->data['uuid'];
            Models\DetailMentor::create([
                'id_users'             => $user->id,
                    'bio'             => request('bio'),
                    'awal_mengajar'             => date('Y-m-d'),
                    'uuid'      => $uuid
                ]);
        }
        
        return response()->json(['message'=>'Success','info'
        => 'Proses Update Berhasil']);
    }

    public function userDataDelete(Request $request)
    {
        if(!$uuid=$request->token){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        if(($user = Models\User::where('uuid',$uuid)->first())==null){
            return response()->json(['message'=>'Failed','info'=>"Token Tidak Sesuai"]);
        }

        $user_del = Models\User::where('uuid',$uuid)->delete();
        
        return response()->json(['message'=>'Success','info'
        => 'Proses Hapus User Berhasil']);
    }

    public function allData(Request $request)
    {
        $list_user = User::all();
        return $list_user;
    }
}