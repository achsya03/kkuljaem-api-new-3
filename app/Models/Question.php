<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $fillable = [
        'pertanyaan_teks',
        'url_gambar',
        'gambar_id',
        'url_file',
        'file_id',
        'jenis_jawaban',
        'jawaban',
        'uuid',
    ];
    public function task()
    {
        return $this->hasMany(Task::class,'id_question','id');
    }
    public function exam()
    {
        return $this->hasMany(Exam::class,'id_question','id');
    }
}
