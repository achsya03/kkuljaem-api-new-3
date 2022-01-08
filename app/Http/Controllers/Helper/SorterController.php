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

                    if(count($kategori_kelas) > 0){
                        for($j=0;$j<count($kategori_kelas);$j++){
                            Models\ClassesCategory::where('id',$kategori_kelas[$j]->id)
                            ->update([
                                'urutan'  => $j+1
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
                            Models\Classes::where('id',$kelas[$j]->id)
                            ->where('id_class_category',$last_id_class_category)
                            ->update([
                                'urutan'  => $num+1
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
                            Models\Classes::where('id',$konten[$j]->id)
                            ->where('id_class',$last_id_class)
                            ->update([
                                'urutan'  => $num+1
                            ]);
                        }
                    }
                }elseif($i == 3){
                    $banner = Models\Banner::orderBy('id','ASC')->get();

                    if(count($banner) > 0){
                        $num = 0;
                        
                        for($j=0;$j<count($banner);$j++){
                            Models\Classes::where('id',$banner[$j]->id)
                            ->update([
                                'urutan'  => $num+1
                            ]);
                        }
                    }
                }elseif($i == 4){
                    $topik = Models\Theme::orderBy('id','ASC')->get();

                    if(count($topik) > 0){
                        $num = 0;
                        
                        for($j=0;$j<count($topik);$j++){
                            Models\Classes::where('id',$topik[$j]->id)
                            ->update([
                                'urutan'  => $num+1
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
                    $kategori_kelas = Models\ClassesCategory::select('id')->orderBy('id','ASC')->get();

                    for($k=0;$k<count($arr_id);$k++){
                        if(!in_array($arr_id[$k],$kategori_kelas)){
                            return response()->json([
                                'message' => 'Failed',
                                'error' => 'ID tidak sesuai'
                            ]);
                        }
                    }

                    if(count($arr_id) > 0){
                        for($j=0;$j<count($arr_id);$j++){
                            Models\ClassesCategory::where('id',$arr_id[$j])
                            ->update([
                                'urutan'  => $j+1
                            ]);
                        }
                    }
                }elseif($i == 1){
                    $kelas = Models\Classes::select('id')->orderBy('id_class_category','ASC')->orderBy('id','ASC')->get();

                    for($k=0;$k<count($arr_id);$k++){
                        if(!in_array($arr_id[$k],$kelas)){
                            return response()->json([
                                'message' => 'Failed',
                                'error' => 'ID tidak sesuai'
                            ]);
                        }
                    }

                    if(count($arr_id) > 0){
                        $num = 0;
                        
                        for($j=0;$j<count($arr_id);$j++){
                                                        
                            Models\Classes::where('id',$arr_id[$j])
                            ->update([
                                'urutan'  => $num+1
                            ]);
                        }
                    }
                }elseif($i == 2){
                    $konten = Models\Content::select('id')->orderBy('id_class','ASC')->orderBy('id','ASC')->get();

                    for($k=0;$k<count($arr_id);$k++){
                        if(!in_array($arr_id[$k],$konten)){
                            return response()->json([
                                'message' => 'Failed',
                                'error' => 'ID tidak sesuai'
                            ]);
                        }
                    }

                    if(count($arr_id) > 0){
                        $num = 0;
                        
                        for($j=0;$j<count($arr_id);$j++){
                            Models\Classes::where('id',$arr_id[$j])
                            ->update([
                                'urutan'  => $num+1
                            ]);
                        }
                    }
                }elseif($i == 3){
                    $banner = Models\Banner::select('id')->orderBy('id','ASC')->get();

                    for($k=0;$k<count($arr_id);$k++){
                        if(!in_array($arr_id[$k],$banner)){
                            return response()->json([
                                'message' => 'Failed',
                                'error' => 'ID tidak sesuai'
                            ]);
                        }
                    }

                    if(count($arr_id) > 0){
                        $num = 0;
                        
                        for($j=0;$j<count($arr_id);$j++){
                            Models\Classes::where('id',$arr_id[$j])
                            ->update([
                                'urutan'  => $num+1
                            ]);
                        }
                    }
                }elseif($i == 4){
                    $topik = Models\Theme::orderBy('id','ASC')->get();

                    for($k=0;$k<count($arr_id);$k++){
                        if(!in_array($arr_id[$k],$topik)){
                            return response()->json([
                                'message' => 'Failed',
                                'error' => 'ID tidak sesuai'
                            ]);
                        }
                    }

                    if(count($arr_id) > 0){
                        $num = 0;
                        
                        for($j=0;$j<count($arr_id);$j++){
                            Models\Classes::where('id',$arr_id[$j])
                            ->update([
                                'urutan'  => $num+1
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
                    $kategori_kelas = Models\ClassesCategory::orderBy('id','ASC')->get();

                    $result = $kategori_kelas;
                }elseif($i == 1){
                    $kelas = Models\Classes::orderBy('id_class_category','ASC')->orderBy('id','ASC')->get();

                    $result = $kelas;
                }elseif($i == 2){
                    $konten = Models\Content::orderBy('id_class','ASC')->orderBy('id','ASC')->get();

                    $result = $konten;
                }elseif($i == 3){
                    $banner = Models\Banner::orderBy('id','ASC')->get();

                    $result = $banner;
                }elseif($i == 4){
                    $topik = Models\Theme::orderBy('id','ASC')->get();

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
