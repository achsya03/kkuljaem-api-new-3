<?php

namespace App\Http\Controllers\Helper;

use App\Models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Facades\Redirect;

class RedirectVideoController extends Controller
{
    public static function getVideos(Request $request){
        //return Session::get('test');
        if(!$uuid_video = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        $uuid_usr = $request->id;
        if(!$uuid_user = Models\VideoSession::where('key',$uuid_usr)->first()){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }else{
                if(!$video = Models\Videos::where('uuid',$uuid_video)->first()){
                    return response()->json([
                        'message' => 'Failed',
                        'error' => 'Token tidak sesuai'
                    ]);
                }
                //unset($_SESSION[$uuid_user]);Session::forget('key');
                //Session::forget($uuid_user);
                //Models\VideoSession::where('key',$uuid_usr)->delete();
                $ctype = "video/mp4";
                header("Content-Type: ".$ctype);
                //$id = "12kB1Y3UxFl5BeKr1FlpXqXl-6avGoNAf";
                //$file_path_name = 'https://drive.google.com/uc?export=preview&id='.$id;
                $vid = "https://www.youtube.com/watch?v=".substr($video->url_video,32);
                $file_path_name = $vid;
                $ops = array(
                    CURLOPT_CUSTOMREQUEST  => "GET",
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_CONNECTTIMEOUT => 120,
                    CURLOPT_TIMEOUT        => 120
                );
                $ch = curl_init($file_path_name);
                curl_setopt_array($ch, $ops);
                $out = curl_exec($ch);
                curl_close($ch);
                $header['content'] = $out;
                Models\VideoSession::where('key',$uuid_usr)->delete();
                
                echo $header;
        }
    }
        public static function getVideo(Request $request){
            //return Session::get('test');
            if(!$uuid_video = $request->token){
                return response()->json([
                    'message' => 'Failed',
                    'error' => 'Token tidak sesuai token'
                ]);
            }
            $uuid_usr = $request->id;
            if(!$uuid_user = Models\VideoSession::where('key',$uuid_usr)->first()){
                return response()->json([
                    'message' => 'Failed',
                    'error' => 'Token tidak sesuai session'
                ]);
            }else{
                    if(!$video = Models\Video::where('uuid',$uuid_video)->first()){
                        return response()->json([
                            'message' => 'Failed',
                            'error' => 'Token tidak sesuai video'
                        ]);
                    }
                    //unset($_SESSION[$uuid_user]);Session::forget('key');
                    //Session::forget($uuid_user);
                    //Models\VideoSession::where('key',$uuid_usr)->delete();
                    $ctype = "video/mp4";
                    header("Content-Type: ".$ctype);
                    //$id = "12kB1Y3UxFl5BeKr1FlpXqXl-6avGoNAf";
                    //$file_path_name = 'https://drive.google.com/uc?export=preview&id='.$id;
                    $vid = "https://www.youtube.com/watch?v=".substr($video->url_video,32);
                    $file_path_name = $vid;
                    $ops = array(
                        CURLOPT_CUSTOMREQUEST  => "GET",
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_CONNECTTIMEOUT => 120,
                        CURLOPT_TIMEOUT        => 120
                    );
                    $ch = curl_init($file_path_name);
                    curl_setopt_array($ch, $ops);
                    $out = curl_exec($ch);
                    curl_close($ch);
                    $header['content'] = $out;
                    Models\VideoSession::where('key',$uuid_usr)->delete();
                    
                echo $header;
            }
    }
}
