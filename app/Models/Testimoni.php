<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimoni extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $table="testimoni";
    protected $fillable = [
        'nama',
        'identitas',
        'testimoni',
        'uuid',
    ];
    
    // public function user()
    // {
    //     return $this->belongsTo(User::class,'id_user','id');
    // }

    // public function classes()
    // {
    //     return $this->belongsTo(Classes::class,'id_class','id');
    // }
}
