<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostImage extends Model
{
    use HasFactory;
    protected $table = 'post_image';
    public $timestamps=false;
    protected $fillable = [
        'id_post',
        'url_gambar',
        'gambar_id',
        'uuid',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class,'id_post','id');
    }
}
