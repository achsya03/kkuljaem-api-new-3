<?php

namespace App\Http\Controllers\Avatar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AvatarController extends Controller
{
    
    public function __construct(Request $request){
        $this->middleware('auth');
    }

    public function getAllAvatarGroup(Request $request){

    }

    public function getDetailAvatarGroup(Request $request){

    }

    public function addAvatarGroup(Request $request){
        
    }

    public function editAvatarGroup(Request $request){
        
    }

    public function deleteAvatarGroup(Request $request){
        
    }

    public function getAvatarByGroup(Request $request){

    }

    public function getDetailAvatar(Request $request){

    }

    public function addAvatar(Request $request){
        
    }

    public function editAvatar(Request $request){
        
    }

    public function deleteAvatar(Request $request){
        
    }
}
