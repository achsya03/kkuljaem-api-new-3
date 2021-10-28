<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Mail\SendMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MailController extends Controller
{
    public function __invoke(Request $request)
    {
    }
    public static function sendEmail($info_penggunas,$stat){
            $judul="";
            $path="";
            $info_pengguna=[];
            if($stat=="verify"){
                $judul="Thank you for signing up with Kkuljaem - Please verify your email address";
                $path="verify-mail";
                $info_pengguna=[
                    'nama' => "Kkuljaem-User",
                    'email' => $info_penggunas['email'],
                    'url' => env('APP_DOMAIN', "https://kkuljaem.xyz").env('APP_PORT', "").'api/auth/'.$path.'?token='.$info_penggunas['web_token'],
                ];
            }elseif ($stat=="forgot-pass") {
                $judul="Reset Password";
                $path="change-password";
                $info_pengguna=[
                    'nama' => "Kkuljaem-User",
                    'email' => $info_penggunas['email'],
                    'url' => env('APP_URL', "https://kkuljaem.xyz").env('APP_PORT', "").''.$path.'?token='.$info_penggunas['web_token'],
                ];
            }

            try{
                $kirim_email=Mail::to($info_pengguna['email'])
                ->send(new SendMail($judul,$info_pengguna,$stat));
            }catch(\Exception $e) {
                return 'Send Again';
            }
            if(empty($kirim_email)){
                return 'Mail Sended';
            }else{
                return 'Send Again';
            }
    }
}
