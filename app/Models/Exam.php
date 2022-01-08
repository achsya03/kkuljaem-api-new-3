<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;
    protected $table = 'exam';
    public $timestamps=false;
    protected $fillable = [
        'id_question',
        'id_quiz',
        'number',
        'uuid',
    ];
    public function quiz()
    {
        return $this->belongsTo(Quiz::class,'id_quiz','id');
    }
    public function question()
    {
        return $this->belongsTo(Question::class,'id_question','id');
    }
}
