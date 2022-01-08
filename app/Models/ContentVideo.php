<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentVideo extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $table = "content_video";
    protected $fillable = [
        'id_class',
        'id_quiz',
        'judul',
        'like_count',
        'comment_count',
        'uuid',
        'uuid',
        'uuid',
    ];


    public function contentQuiz()
    {
        return $this->belongsTo(Models\Question::class,'id_quiz','id');
    }

    public function classes()
    {
        return $this->belongsTo(Models\Classes::class,'id_class','id');
    }

    public function studentVideo()
    {
        return $this->hasMany(Models\StudentVideo::class,'id_content_video','id');
    }
}
