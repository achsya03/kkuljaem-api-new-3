<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FCMController extends Controller
{
    public function index(Request $request){
        return view('fcm');
    }

    public static function sendNotification($user,$datas){
        $token = $user->device_id;  
        $from = env('NOTIF_TOKEN', '#');
        $ket = '';
        if(isset($datas['deskripsi'])){
            $ket = $datas['deskripsi'];
        }elseif(isset($datas['keterangan'])){
            $ket = $datas['keterangan'];
        }
        $msg = array
              (
                'body'  => $ket,
                'title' => $datas['judul'],
                'receiver' => $user->nama,
                'icon'  => env('APP_DOMAIN', "https://kkuljaem.xyz").'icon/kkIcon.png',/*Default Icon*/
                'sound' => 'mySound'/*Default sound*/
              );

        $fields = array
                (
                    'to'        => $token,
                    'notification'  => $msg
                );

        $headers = array
                (
                    'Authorization: key=' . $from,
                    'Content-Type: application/json'
                );
        //#Send Reponse To FireBase Server 
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, env('NOTIF_URL', '#') );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        //dd($result);
        curl_close( $ch );
        return $result;
    }

    public static function sendLotNotification($device_id,$judul,$deskripsi){
        $token = $device_id;  
        $from = env('NOTIF_TOKEN', '#');
        $msg = array
              (
                'body'  => $deskripsi,
                'title' => $judul,
                'receiver' => 'All Users',
                'icon'  => env('APP_DOMAIN', "https://kkuljaem.xyz").'icon/kkIcon.png',/*Default Icon*/
                'sound' => 'mySound'/*Default sound*/
              );

        $fields = array
                (
                    'to'        => $token,
                    'notification'  => $msg
                );

        $headers = array
                (
                    'Authorization: key=' . $from,
                    'Content-Type: application/json'
                );
        //#Send Reponse To FireBase Server 
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, env('NOTIF_URL', '#') );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        //dd($result);
        curl_close( $ch );
        return $result;
    }
}
