<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $table = 'post';
    public $timestamps=false;
    protected $fillable = [
        'id_user',
        'id_theme',
        'judul',
        'jenis',
        'deskripsi',
        'jml_like',
        'jml_komen',
        'stat_post',
        'stat_terpilih',
        'uuid',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'id_user','id');
    }

    public function theme()
    {
        return $this->belongsTo(Theme::class,'id_theme','id');
    }

    public function postImage()
    {
        return $this->hasMany(PostImage::class,'id_post','id');
    }

    public function comment()
    {
        return $this->hasMany(Comment::class,'id_post','id');
    }

    public function postAlert()
    {
        return $this->hasMany(PostAlert::class,'id_post','id');
    }

    public function postLike()
    {
        return $this->hasMany(PostLike::class,'id_post','id');
    }
}
