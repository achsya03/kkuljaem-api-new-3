<?php

namespace App\Http\Controllers\Auth;

use App\Models\DetailMentor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\MailController;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Validator;

class DetailMentorController extends Controller
{

    public static function getUuid(){
        $uuid = (string) str_replace('-','',Str::uuid());

        $uuid_exist = count(DetailMentor::where('uuid',$uuid)->get());
        while ($uuid_exist > 0) {
            $uuid = (string) str_replace('-','',Str::uuid());
            $uuid_exist = count(DetailMentor::where('uuid',$uuid)->get());
        }

        return $uuid;
    }

    public static function addData($data_user){
        DetailMentor::create([
            'id_users' => $data_user['id_users'],
            'bio' => $data_user['bio'],
            'awal_mengajar' => $data_user['awal_mengajar'],
            'url_foto' => $data_user['url_foto'],
            'foto_id' => $data_user['foto_id'],
            'uuid' => $data_user['uuid']
        ]);
    }
}
