<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentQuiz extends Model
{
    use HasFactory;

    public $timestamps=false;
    protected $table = "content_quiz";
    protected $fillable = [
        'id_question',
        'judul',
        'keterangan',
        'jml_pertanyaan',
        'uuid',
    ];

    public function question()
    {
        return $this->belongsTo(Models\Question::class);
    }

    public function classes()
    {
        return $this->hasMany(Classes::class);
    }

    public function contentVideo()
    {
        return $this->hasMany(Models\ContentVideo::class);
    }
}
