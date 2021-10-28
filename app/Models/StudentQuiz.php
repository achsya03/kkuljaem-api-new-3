<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentQuiz extends Model
{
    use HasFactory;
    protected $table = 'student_quiz';
    public $timestamps=false;
    protected $fillable = [
        'id_student',
        'id_quiz',
        'register_date',
        'nilai',
        'uuid'
    ];
    public function quiz()
    {
        return $this->belongsTo(Quiz::class,'id_quiz','id');
    }
    public function student()
    {
        return $this->belongsTo(Student::class,'id_student','id');
    }
}
