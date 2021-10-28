<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
    use HasFactory;
    protected $table = 'post_like';
    public $timestamps=false;
    protected $fillable = [
        'id_user',
        'id_post',
        'uuid',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class,'id_post','id');
    }
}
