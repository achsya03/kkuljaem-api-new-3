<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentAlert extends Model
{
    use HasFactory;
    protected $table = 'comment_alert';
    public $timestamps=false;
    protected $fillable = [
        'id_user',
        'id_comment',
        'komentar',
        'alert_status',
        'uuid',
    ];

    public function comment()
    {
        return $this->belongsTo(Comment::class,'id_comment','id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'id_user','id');
    }
}
