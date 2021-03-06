<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvatarGroup extends Model
{
    use HasFactory;
    protected $table = 'avatar_group';
    public $timestamps=false;
    protected $fillable = [
        'id',
        'nama',
        'deskripsi',
        'uuid',
    ];
    public function avatar()
    {
        return $this->hasMany(Avatar::class,'id_avatar_group','id');
    }
}
