<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;
    protected $table = 'quiz';
    public $timestamps=false;
    protected $fillable = [
        'id_content',
        'judul',
        'keterangan',
        'jml_pertanyaan',
        'uuid'
    ];
    public function content()
    {
        return $this->belongsTo(Content::class,'id_content','id');
    }
    public function studentQuiz()
    {
        return $this->belongsTo(StudentQuiz::class,'id_quiz','id');
    }
    public function exam()
    {
        return $this->hasMany(Exam::class,'id_quiz','id');
    }
}
