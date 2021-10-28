<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $table = 'students';
    public $timestamps=false;
    protected $fillable = [
        'id_user',
        'id_class',
        'register_date',
        'jml_pengerjaan',
        'uuid'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'id_user','id');
    }
}
