<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailMentor extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $fillable = [
        'id_users',
        'detailMentor',
        'bio',
        'url_foto',
        'foto_id',
        'awal_mengajar',
        'uuid',
    ];

    public function teacher()
    {
        return $this->hasMany(Teacher::class,'id_mentor','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'id_users','id');
    }
}
