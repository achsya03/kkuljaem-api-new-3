<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForceLog extends Model
{
    use HasFactory;
    protected $table = 'force_log';
    public $timestamps=false;
    protected $fillable = [
        'id_detail_student',
        'id_detail_mentor',
        'note',
        'uuid',
    ];
    public function detail_student()
    {
        return $this->belongsTo(DetailStudent::class,'id_detail_student','id');
    }
    public function detail_mentor()
    {
        return $this->belongsTo(DetailMentor::class,'id_detail_mentor','id');
    }
}
