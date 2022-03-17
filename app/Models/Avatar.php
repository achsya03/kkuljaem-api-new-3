<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avatar extends Model
{
    use HasFactory;
    protected $table = 'avatar';
    public $timestamps=false;
    protected $fillable = [
        'id',
        'id_avatar_group',
        'nama',
        'deskripsi',
        'avatar_url',
        'avatar_id',
        'uuid',
    ];
}
