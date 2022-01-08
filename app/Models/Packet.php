<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Packet extends Model
{
    use HasFactory;
    protected $table = 'packet';
    public $timestamps=false;
    protected $fillable = [
        'nama',
        'deskripsi',
        'lama_paket',
        'harga',
        'status_aktif',
        'uuid'
    ];
    
    public function subs()
    {
        return $this->hasMany(Subs::class,'id_packet','id');
    }
}
