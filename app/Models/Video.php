<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;
    protected $table = 'video';
    public $timestamps=false;
    protected $fillable = [
        'id_content',
        'judul',
        'keterangan',
        'jml_latihan',
        'jml_shadowing',
        //'jml_pertanyaan',
        'url_video',
        'uuid'
    ];
    public function content()
    {
        return $this->belongsTo(Content::class,'id_content','id');
    }
    public function videoTheme()
    {
        return $this->hasMany(VideoTheme::class,'id_video','id');
    }
    public function studentVideo()
    {
        return $this->hasMany(StudentVideo::class,'id_video','id');
    }
}
