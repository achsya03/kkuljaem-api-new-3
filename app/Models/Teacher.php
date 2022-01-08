<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $fillable = [
        'id_user',
        'id_class',
        'uuid',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'id_user','id');
    }

    public function classes()
    {
        return $this->belongsTo(Classes::class,'id_class','id');
    }
}
