<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shadowing extends Model
{
    use HasFactory;
    protected $table = 'shadowing';
    public $timestamps=false;
    protected $fillable = [
        'id_word',
        'id_video',
        'number',
        'uuid'
    ];
    public function word()
    {
        return $this->belongsTo(Words::class,'id_word','id');
    }
    public function video()
    {
        return $this->belongsTo(Video::class,'id_video','id');
    }
}
