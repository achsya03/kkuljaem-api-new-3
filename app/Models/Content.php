<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;
    protected $table = 'content';
    public $timestamps=false;
    protected $fillable = [
        'id_class',
        'number',
        'type',
        'uuid'
    ];
    public function classes()
    {
        return $this->belongsTo(Classes::class,'id_class','id');
    }
    public function quiz()
    {
        return $this->hasMany(Quiz::class,'id_content','id');
    }
    public function video()
    {
        return $this->hasMany(Video::class,'id_content','id');
    }
}
