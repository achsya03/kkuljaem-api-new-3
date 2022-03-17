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
    ];
}
