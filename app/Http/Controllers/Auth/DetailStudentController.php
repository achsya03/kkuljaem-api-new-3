<?php

namespace App\Http\Controllers\Auth;

use App\Models\DetailStudent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\MailController;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Validator;

class DetailStudentController extends Controller
{
    public static function getUuid(){
        $uuid = (string) str_replace('-','',Str::uuid());

        $uuid_exist = count(DetailStudent::where('uuid',$uuid)->get());
        while ($uuid_exist > 0) {
            $uuid = (string) str_replace('-','',Str::uuid());
            $uuid_exist = count(DetailStudent::where('uuid',$uuid)->get());
        }

        return $uuid;
    }

    public static function addData($data_user){
        DetailStudent::create([
            'id_users' => $data_user['id_users'],
            'alamat' => $data_user['alamat'],
            'jenis_kel' => $data_user['jenis_kel'],
            'tgl_lahir' => $data_user['tgl_lahir'],
            'tempat_lahir' => $data_user['tempat_lahir'],
            'uuid' => $data_user['uuid']
        ]);
    }
}
