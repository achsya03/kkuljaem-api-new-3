<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reference extends Model
{
    use HasFactory;
    protected $table = 'reference';
    public $timestamps=false;
    protected $fillable = [
        'nama',
        'kode',
        'tgl_aktif',
        'status',
        'uuid'
    ];
    public function subs()
    {
        return $this->hasMany(Subs::class,'id_reference','id');
    }
}
