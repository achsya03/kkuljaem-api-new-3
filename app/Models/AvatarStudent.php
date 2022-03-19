<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvatarStudent extends Model
{
    use HasFactory;
    protected $table = 'avatar_student';
    public $timestamps=false;
    protected $fillable = [
        'id',
        'id_avatar',
        'id_detail_student',
        'uuid',
    ];public function avatar()
    {
        return $this->belongsTo(Avatar::class,'id_avatar','id');
    }
    public function detailStudent()
    {
        return $this->belongsTo(DetailStudent::class,'id_detail_student','id');
    }
}
