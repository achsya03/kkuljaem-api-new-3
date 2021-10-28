<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $table = 'task';
    public $timestamps=false;
    protected $fillable = [
        'id_question',
        'id_video',
        'number',
        'uuid'
    ];
    public function question()
    {
        return $this->belongsTo(Question::class,'id_question','id');
    }
    public function video()
    {
        return $this->belongsTo(Video::class,'id_video','id');
    }
}
