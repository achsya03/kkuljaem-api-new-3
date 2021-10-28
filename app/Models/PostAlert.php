<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostAlert extends Model
{
    use HasFactory;
    protected $table = 'post_alert';
    public $timestamps=false;
    protected $fillable = [
        'id_user',
        'id_post',
        'komentar',
        'alert_status',
        'uuid',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class,'id_post','id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'id_user','id');
    }
}
