<?php

namespace App\Http\Controllers\Helper;

use App\Models;
use App\Http\Controllers\Controller;
use App\Http\Controllers;
use App\Http\Controllers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SorterController extends Controller
{
    public function __construct(Request $request){
        $this->middleware('auth');
    }

    public function setAutoNumbering(Request $request){
        if(!$table = $request->table){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Table tidak sesuai'
            ]);
        }

        $tables = array('kategori_kelas','kelas','konten','banner','topik');

        if(!in_array($table,$tables)){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Table tidak sesuai'
            ]);
        }

        for($i=0;$i<count($tables);$i++){
            if($table == $tables[$i]){
                if($i == 0){
                    $kategori_kelas = Models\ClassesCategory::orderBy('id','ASC')->get();
                    $num = 0;
                    if(count($kategori_kelas) > 0){
                        for($j=0;$j<count($kategori_kelas);$j++){
                            $num += 1;
                            Models\ClassesCategory::where('id',$kategori_kelas[$j]->id)
                            ->update([
                                'urutan'  => $num
                            ]);
                        }
                    }
                }elseif($i == 1){
                    $kelas = Models\Classes::orderBy('id_class_category','ASC')->orderBy('id','ASC')->get();

                    if(count($kelas) > 0){
                        $num = 0;
                        $last_id_class_category = $kelas[0]->id_class_category;
                        for($j=0;$j<count($kelas);$j++){
                            if($last_id_class_category != $kelas[$j]->id_class_category){
                                $num = 0;
                                $last_id_class_category = $kelas[$j]->id_class_category;
                            }
                            $num += 1;
                            Models\Classes::where('id',$kelas[$j]->id)
                            ->where('id_class_category',$last_id_class_category)
                            ->update([
                                'urutan'  => $num
                            ]);
                        }
                    }
                }elseif($i == 2){
                    $konten = Models\Content::orderBy('id_class','ASC')->orderBy('id','ASC')->get();

                    if(count($konten) > 0){
                        $num = 0;
                        $last_id_class= $konten[0]->id_class;
                        for($j=0;$j<count($konten);$j++){

                            if($last_id_class != $konten[$j]->id_class){
                                $num = 0;
                                $last_id_class = $konten[$j]->id_class;
                            }
                            $num += 1;
                            Models\Content::where('id',$konten[$j]->id)
                            ->where('id_class',$last_id_class)
                            ->update([
                                'number'  => $num
                            ]);
                        }
                    }
                }elseif($i == 3){
                    $banner = Models\Banner::orderBy('id','ASC')->get();

                    if(count($banner) > 0){
                        $num = 0;
                        
                        for($j=0;$j<count($banner);$j++){
                            $num += 1;
                            Models\Banner::where('id',$banner[$j]->id)
                            ->update([
                                'urutan'  => $num
                            ]);
                        }
                    }
                }elseif($i == 4){
                    $video_uuid = Models\Video::select('uuid')->get();
                    $topik = Models\Theme::orderBy('id','ASC')->whereNotIn('judul',$video_uuid)->get();

                    if(count($topik) > 0){
                        $num = 0;
                        
                        for($j=0;$j<count($topik);$j++){
                            $num += 1;
                            Models\Theme::where('id',$topik[$j]->id)
                            ->update([
                                'urutan'  => $num
                            ]);
                        }
                    }
                }
            }
        }

        return response()->json([
            'message' => 'Success',
            'info' => 'Auto Update Berhasil'
        ]);
    }

    public function setNumbering(Request $request){
        if(!$table = $request->table){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Table tidak sesuai'
            ]);
        }
        if(!$arr_id = $request->arr_id){
            return response()->json([
                'message' => 'Failed',
                'error' => 'ID tidak sesuai'
            ]);
        }

        $tables = array('kategori_kelas','kelas','konten','banner','topik');

        if(!in_array($table,$tables)){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Table tidak sesuai'
            ]);
        }

        for($i=0;$i<count($tables);$i++){
            if($table == $tables[$i]){
                if($i == 0){
                    $kategori_kelas = Models\ClassesCategory::select('uuid')->orderBy('id','ASC')->get();

                    $bann = [];
                    for($k=0;$k<count($kategori_kelas);$k++){
                        array_push($bann,$kategori_kelas[0]->uuid);
                    }
                    for($k=0;$k<count($arr_id);$k++){
                        if(!in_array($arr_id[$k],$bann)){
                            return response()->json([
                                'message' => 'Failed',
                                'error' => 'ID tidak sesuai'
                            ]);
                        }
                    }

                    if(count($arr_id) != count($kategori_kelas)){
                        return response()->json([
                            'message' => 'Failed',
                            'error' => 'Jumlah ID tidak sesuai'
                        ]);
                    }

                    if(count($arr_id) > 0){
                        $num = 0;
                        for($j=0;$j<count($arr_id);$j++){
                            $num += 1;
                            Models\ClassesCategory::where('uuid',$arr_id[$j])
                            ->update([
                                'urutan'  => $num
                            ]);
                        }
                    }
                }elseif($i == 1){
                    if(!$detail_kategori = $request->detail_kategori){
                        return response()->json([
                            'message' => 'Failed',
                            'error' => 'Detail Kategori tidak sesuai'
                        ]);
                    }
                    $kategori_kelas = Models\ClassesCategory::select('id','nama','uuid')->where('uuid',$detail_kategori)->get();
                    if(count($kategori_kelas)==0){
                        return response()->json([
                            'message' => 'Failed',
                            'error' => 'Detail Kategori tidak sesuai'
                        ]);
                    }
                    $kelas = Models\Classes::select('uuid')->where('id_class_category',$kategori_kelas[0]->id)->orderBy('id','ASC')->get();

                    $bann = [];
                    for($k=0;$k<count($kelas);$k++){
                        array_push($bann,$kelas[$k]->uuid);
                    }
                    for($k=0;$k<count($arr_id);$k++){
                        if(!in_array($arr_id[$k],$bann)){
                            return response()->json([
                                'message' => 'Failed',
                                'error' => 'ID tidak sesuai'
                            ]);
                        }
                    }


                    if(count($arr_id) != count($kelas)){
                        return response()->json([
                            'message' => 'Failed',
                            'error' => 'Jumlah ID tidak sesuai'
                        ]);
                    }

                    if(count($arr_id) > 0){
                        $num = 0;
                        
                        for($j=0;$j<count($arr_id);$j++){
                            $num += 1;
                                                        
                            Models\Classes::where('uuid',$arr_id[$j])
                            ->update([
                                'urutan'  => $num
                            ]);
                        }
                    }
                }elseif($i == 2){
                    if(!$detail_kelas = $request->detail_kelas){
                        return response()->json([
                            'message' => 'Failed',
                            'error' => 'Detail Kelas tidak sesuai'
                        ]);
                    }
                    $kelas = Models\Classes::select('id','nama','uuid')->where('uuid',$detail_kelas)->get();
                    if(count($kelas)==0){
                        return response()->json([
                            'message' => 'Failed',
                            'error' => 'Detail Kategori tidak sesuai'
                        ]);
                    }
                    $cont = [];
                    $konten = Models\Content::select('uuid')->where('id_class',$kelas[0]->id)->orderBy('id','ASC')->get();

                    $bann = [];
                    for($k=0;$k<count($konten);$k++){
                        array_push($bann,$konten[$k]->uuid);
                    }
                    for($k=0;$k<count($arr_id);$k++){
                        if(!in_array($arr_id[$k],$bann)){
                            return response()->json([
                                'message' => 'Failed',
                                'error' => 'ID tidak sesuai'
                            ]);
                        }
                    }

                    if(count($arr_id) != count($konten)){
                        return response()->json([
                            'message' => 'Failed',
                            'error' => 'Jumlah ID tidak sesuai'
                        ]);
                    }

                    if(count($arr_id) > 0){
                        $num = 0;
                        
                        for($j=0;$j<count($arr_id);$j++){
                            $num += 1;

                            Models\Content::where('uuid',$arr_id[$j])
                            ->update([
                                'number'  => $num
                            ]);
                        }
                    }
                }elseif($i == 3){
                    $banner = Models\Banner::select('uuid')->orderBy('id','ASC')->get();
                    $bann = [];
                    for($k=0;$k<count($banner);$k++){
                        array_push($bann,$banner[$k]->uuid);
                    }
                    for($k=0;$k<count($arr_id);$k++){
                        if(!in_array($arr_id[$k],$bann)){
                            return response()->json([
                                'message' => 'Failed',
                                'error' => 'ID tidak sesuai'
                            ]);
                        }
                    }
                    
                    if(count($arr_id) != count($banner)){
                        return response()->json([
                            'message' => 'Failed',
                            'error' => 'Jumlah ID tidak sesuai'
                        ]);
                    }

                    if(count($arr_id) > 0){
                        $num = 0;
                        
                        for($j=0;$j<count($arr_id);$j++){
                            $num += 1;
                            Models\Banner::where('uuid',$arr_id[$j])
                            ->update([
                                'urutan'  => $num
                            ]);
                        }
                    }
                }elseif($i == 4){
                    $topik = Models\Theme::select('uuid')->orderBy('id','ASC')->get();

                    $bann = [];
                    for($k=0;$k<count($topik);$k++){
                        array_push($bann,$topik[$k]->uuid);
                    }
                    for($k=0;$k<count($arr_id);$k++){
                        if(!in_array($arr_id[$k],$bann)){
                            return response()->json([
                                'message' => 'Failed',
                                'error' => 'ID tidak sesuai'
                            ]);
                        }
                    }

                    if(count($arr_id) != count($topik)){
                        return response()->json([
                            'message' => 'Failed',
                            'error' => 'Jumlah ID tidak sesuai'
                        ]);
                    }

                    if(count($arr_id) > 0){
                        $num = 0;
                        
                        for($j=0;$j<count($arr_id);$j++){
                            $num += 1;
                            Models\Theme::where('uuid',$arr_id[$j])
                            ->update([
                                'urutan'  => $num
                            ]);
                        }
                    }
                }
            }
        }


        return response()->json([
            'message' => 'Success',
            'info' => 'Update Berhasil'
        ]);
    }

    public function showData(Request $request){

        if(!$table = $request->table){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Table tidak sesuai'
            ]);
        }

        $tables = array('kategori_kelas','kelas','konten','banner','topik');
        $result = [];

        if(!in_array($table,$tables)){
            return response()->json([
                'message' => 'Failed',
                'error' => 'Table tidak sesuai'
            ]);
        }for($i=0;$i<count($tables);$i++){
            if($table == $tables[$i]){
                if($i == 0){
                    $kategori_kelas = Models\ClassesCategory::select('nama','urutan')->orderBy('urutan','ASC')->get();

                    $result = $kategori_kelas;
                }elseif($i == 1){
                    if(!$detail_kategori = $request->detail_kategori){
                        return response()->json([
                            'message' => 'Failed',
                            'error' => 'Detail Kategori tidak sesuai'
                        ]);
                    }
                    $kategori_kelas = Models\ClassesCategory::select('id','nama','uuid')->where('uuid',$detail_kategori)->get();
                    if(count($kategori_kelas)==0){
                        return response()->json([
                            'message' => 'Failed',
                            'error' => 'Detail Kategori tidak sesuai'
                        ]);
                    }
                    $kelas = Models\Classes::select('nama','urutan')->where('id_class_category',$kategori_kelas[0]->id)->orderBy('urutan','ASC')->get();
                    unset($kategori_kelas[0]->id);
                    $result['kategori_kelas'] = $kategori_kelas[0];
                    $result['kelas'] = $kelas;
                }elseif($i == 2){
                    if(!$detail_kelas = $request->detail_kelas){
                        return response()->json([
                            'message' => 'Failed',
                            'error' => 'Detail Kelas tidak sesuai'
                        ]);
                    }

                    $kelas = Models\Classes::select('id','nama','uuid')->where('uuid',$detail_kelas)->get();
                    if(count($kelas)==0){
                        return response()->json([
                            'message' => 'Failed',
                            'error' => 'Detail Kategori tidak sesuai'
                        ]);
                    }
                    $cont = [];
                    $content = Models\Content::where('id_class',$kelas[0]->id)->orderBy('number','ASC')->get();
                    $count_vid = 0;
                    $count_quiz = 0;
                    
                    for($i = 0;$i < count($content);$i++){
                        $arr1 = [];
                        if($content[$i]->type == 'video'){
                            $count_vid += 1;
                            $content_video = Models\Video::where('id_content',$content[$i]->id)->get();
                            $arr1['urutan'] = $content[$i]->number;
                            $arr1['judul'] = $content_video[0]->judul;
                            $arr1['type'] = $content[$i]->type;
                           
                            $arr1['content_video_uuid'] = $content_video[0]->uuid;
                        }elseif($content[$i]->type == 'quiz'){
                            $count_quiz += 1;
                            $content_quiz = Models\Quiz::where('id_content',$content[$i]->id)->get();
                            $arr1['urutan'] = $content[$i]->number;
                            $arr1['judul'] = $content_quiz[0]->judul;
                            $arr1['type'] = $content[$i]->type;
                            
                            $arr1['content_quiz_uuid'] = $content_quiz[0]->uuid;
                        }
                        $cont[$i] = $arr1;
                    }
                    unset($kelas[0]->id);
                    $result['kelas'] = $kelas[0];
                    $result['konten'] = $cont;
                }elseif($i == 3){
                    $banner = Models\Banner::select('judul_banner','urutan')->orderBy('urutan','ASC')->get();

                    $result = $banner;
                }elseif($i == 4){
                    $video_uuid = Models\Video::select('uuid')->get();
                    $topik = Models\Theme::select('judul','urutan')->orderBy('urutan','ASC')->whereNotIn('judul',$video_uuid)->get();
                    $result = $topik;
                }
            }
        }

        return response()->json([
            'message' => 'Success',
            'data' => $result
        ]);
    }
}
