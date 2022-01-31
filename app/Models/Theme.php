<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;
    protected $table = 'theme';
    public $timestamps=false;
    protected $fillable = [
        'judul',
        'jml_post',
        'jml_like',
        'jml_comment',
        'url_gambar',
        'gambar_id',
        'urutan',
        'uuid',
    ];

    public function post()
    {
        return $this->hasMany(Post::class,'id_theme','id');
    }
    public function videoTheme()
    {
        return $this->hasMany(VideoTheme::class,'id_theme','id');
    }
}
