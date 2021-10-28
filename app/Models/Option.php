<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $fillable = [
        'id_question',
        'jawaban_id',
        'jawaban_teks',
        'url_gambar',
        'gambar_id',
        'url_file',
        'file_id',
        'uuid',
    ];
    public function question()
    {
        return $this->belongsTo(Question::class,'id_question','id');
    }
}
