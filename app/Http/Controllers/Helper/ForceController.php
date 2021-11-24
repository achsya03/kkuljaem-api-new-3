<?php

namespace App\Http\Controllers\Helper;

use App\Models;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use General;

class ForceController extends Controller
{
    public function __construct(Request $request){
        $this->middleware('auth');
    }

    public function forceSubs(Request $request){
        $result = [];
        if($request->user()->jenis_pengguna != 2){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Anda bukan admin'
            ]);
        }
        if(!$token = $request->token){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Token tidak sesuai'
            ]);
        }
        if($token != date("Y__m__")){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Format token tidak sesuai'
            ]);
        }
        if(!$email = $request->email){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Email tidak sesuai'
            ]);
        }  
        if(count($user = Models\User::where('email',$email)->get())==0){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Email tidak sesuai'
            ]);
        }
        if(date_format(date_create($request->tgl_akhir),"Y/m/d")<date("Y/m/d")){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Tanggal Akhir Harus Lebih Dari Hari ini'
            ]);
        }
        $tgl_akhir = date_format(date_create($request->tgl_akhir),"Y/m/d");
        $tgl_akh = (new \DateTime(date('Y-m-d')))->modify('+'.(30*1).' day')->format('Y-m-d');
            
        $user = Models\User::where('email',$email)->update([
            'tgl_langganan_akhir' => $tgl_akhir
        ]);

        $result = [
            'email' => $email,
            'tgl_langganan_akhir' => $tgl_akhir
        ];

        return response()->json([
			'message' => 'Success',
			'info' => 'Proses Update Berhasil',
			'data' => $result
		]);
    }

    public function forceWordUrl(Request $request){
        $result = [];
        $word = Models\Words::orderBy('id','ASC')->get();

        $aa = [];
        $bb = [];

        for($i=0;$i<count($word);$i++){
            if(substr($word[$i]->url_pengucapan, 0, 11) == 'https://res'){
                // $aa[$i] = substr($word[$i]->url_pengucapan, 0, 11);
                // $bb[$i] = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($word[$i]->url_pengucapan, 69);
                $url = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($word[$i]->url_pengucapan, 69);
                $aa[$i] = $word[$i]->id;
                $update = Models\Words::where('id',$word[$i]->id)
                    ->update([
                        'url_pengucapan' => $url
                    ]);
            }
        }

        return response()->json([
			'message' => 'Success',
			'info' => 'Proses Update Berhasil',
		]);
    }

    public function forcePostUrl(Request $request){
        $result = [];
        $post_image = Models\PostImage::orderBy('id','ASC')->get();

        $aa = [];
        $bb = [];

        for($i=0;$i<count($post_image);$i++){
            if(substr($post_image[$i]->url_gambar, 0, 11) == 'https://res'){
                // $aa[$i] = substr($word[$i]->url_pengucapan, 0, 11);
                // $bb[$i] = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($word[$i]->url_pengucapan, 69);
                $url = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($post_image[$i]->url_gambar, 69);
                $aa[$i] = $post_image[$i]->id;
                $update = Models\PostImage::where('id',$post_image[$i]->id)
                    ->update([
                        'url_gambar' => $url
                    ]);
            }
        }

        return response()->json([
			'message' => 'Success',
			'info' => 'Proses Update Berhasil',
		]);
    }

    public function forceBannerUrl(Request $request){
        $result = [];
        $banner = Models\Banner::orderBy('id','ASC')->get();

        $aa = [];
        $bb = [];

        for($i=0;$i<count($banner);$i++){
            if(substr($banner[$i]->url_web, 0, 11) == 'https://res'){
                // $aa[$i] = substr($word[$i]->url_pengucapan, 0, 11);
                // $bb[$i] = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($word[$i]->url_pengucapan, 69);
                $url1 = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($banner[$i]->url_web, 69);
                $url2 = 'https://kkuljaem-space.sfo3.digitaloceanspaces.com'.substr($banner[$i]->url_mobile, 69);
                $aa[$i] = $banner[$i]->id;
                $update = Models\Banner::where('id',$banner[$i]->id)
                    ->update([
                        'url_web' => $url1,
                        'url_mobile' => $url2,
                    ]);
            }
        }

        return response()->json([
			'message' => 'Success',
			'info' => 'Proses Update Berhasil',
		]);
    }
}
