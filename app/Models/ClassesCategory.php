<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassesCategory extends Model
{
    use HasFactory;
    public $timestamps=false;
    protected $table="class_category";
    protected $fillable = [
        'nama',
        'deskripsi',
        'uuid',
        'urutan',
    ];

    public function classes()
    {
        return $this->hasMany(Classes::class,'id_class_category','id');
    }
}
