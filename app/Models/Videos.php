<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Videos extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $fillable = [
        'jadwal',
        'url_video',
        'uuid',
    ];

    public function sch_videos(){
        return $this->hasMany(Sch_Videos::class,'id_videos','id');
    }
}
