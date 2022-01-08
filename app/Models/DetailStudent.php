<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailStudent extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $fillable = [
        'id_users',
        'alamat',
        'jenis_kel',
        'tgl_lahir',
        'tempat_lahir',
        'url_foto',
        'foto_id',
        'uuid',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
