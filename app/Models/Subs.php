<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subs extends Model
{
    use HasFactory;
    protected $table = 'subs';
    public $timestamps=false;
    protected $fillable = [
        'id_user',
        'id_packet',
        'id_reference',
        'harga',
        'diskon',
        'snap_token',
        'snap_url',
        'subs_status',
        'tgl_subs',
        'tgl_akhir_bayar',
        'uuid'
    ];
    public function payment()
    {
        return $this->hasMany(Payment::class,'id_subs','id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'id_user','id');
    }
    public function packet()
    {
        return $this->belongsTo(Packet::class,'id_packet','id');
    }
    public function reference()
    {
        return $this->belongsTo(Reference::class,'id_reference','id');
    }
}
