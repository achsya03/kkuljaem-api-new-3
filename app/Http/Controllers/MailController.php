<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Mail\SendMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Config;

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
                $judul="Selamat bergabung dengan Kkuljaem Korean";
                $path="verify-mail";
                $info_pengguna=[
                    'nama' => "Kkuljaem-User",
                    'email' => $info_penggunas['email'],
                    'url' => env('APP_DOMAIN', "https://kkuljaem.xyz").env('APP_PORT', "").'api/auth/'.$path.'?token='.$info_penggunas['web_token'],
                ];
            }elseif ($stat=="forgot-pass") {
                $judul="Ubah kata sandi untuk akun Kkuljaem Korean";
                $path="change-password";
                $info_pengguna=[
                    'nama' => "Kkuljaem-User",
                    'email' => $info_penggunas['email'],
                    'url' => env('APP_URL', "https://kkuljaem.xyz").env('APP_PORT', "").''.$path.'?token='.$info_penggunas['web_token'],
                ];
            }

            $res = 'Send Again';

            #$MAIL_MAILER = explode(",",env('MAIL_MAILER'));
            $MAIL_HOST_ARR = explode(",-",env('MAIL_HOST_ARR'));
            $MAIL_PORT_ARR = explode(",-",env('MAIL_PORT_ARR'));
            $MAIL_USERNAME_ARR = explode(",-",env('MAIL_USERNAME_ARR'));
            $MAIL_PASSWORD_ARR = explode(",-",env('MAIL_PASSWORD_ARR'));
            #$MAIL_ENCRYPTION = explode(",",env('MAIL_ENCRYPTION'));
            $MAIL_FROM_ADDRESS_ARR = explode(",-",env('MAIL_FROM_ADDRESS_ARR'));
            #$MAIL_FROM_NAME = explode(",",env('MAIL_FROM_NAME'));

            $counter = 0;

            // for($i=0;$i<count($MAIL_HOST_ARR);$i++){
            //     $counter = $i;
            //     #putenv("MAIL_MAILER=".$MAIL_MAILER[$counter]);
            //     Config::set('mail.host', $MAIL_HOST_ARR[$counter]);
            //     Config::set('mail.port', $MAIL_PORT_ARR[$counter]);
            //     Config::set('mail.username', $MAIL_USERNAME_ARR[$counter]);
            //     Config::set('mail.password', $MAIL_PASSWORD_ARR[$counter]);
            //     #putenv("MAIL_ENCRYPTION=".$MAIL_ENCRYPTION[$counter]);
            //     Config::set('mail.address', $MAIL_FROM_ADDRESS_ARR[$counter]);
                #putenv("MAIL_FROM_NAME=".$MAIL_FROM_NAME[$counter]);
                try{

                    $kirim_email=Mail::to($info_pengguna['email'])
                    ->send(new SendMail($judul,$info_pengguna,$stat));
                }catch(\Exception $e) {
                    $res = 'Send Again';
                }
                if(empty($kirim_email)){
                    $res = 'Mail Sended';
                    //break;
                }else{
                    $res = 'Send Again';
                }
            // }
            return $res;
    }
}
