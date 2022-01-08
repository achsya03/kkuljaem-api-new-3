<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentVideo extends Model
{
    use HasFactory;
    protected $table = 'student_video';
    public $timestamps=false;
    protected $fillable = [
        'id_student',
        'id_video',
        'register_date',
        'uuid'
    ];
    public function video()
    {
        return $this->belongsTo(Video::class,'id_video','id');
    }
    public function student()
    {
        return $this->belongsTo(Student::class,'id_student','id');
    }
}
