<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $table = 'comment';
    protected $fillable = [
        'id_user',
        'id_post',
        'comment',
        'stat_comment',
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
    public function commentAlert()
    {
        return $this->hasMany(CommentAlert::class,'id_comment','id');
    }
}
