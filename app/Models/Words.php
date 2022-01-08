<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Words extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $fillable = [
        'jadwal',
        'hangeul',
        'pelafalan',
        'penjelasan',
        'url_pengucapan',
        'pengucapan_id',
        'uuid',
    ];

    public function sch_words(){
        return $this->hasMany(Sch_Words::class,'id_words','id');
    }
}
