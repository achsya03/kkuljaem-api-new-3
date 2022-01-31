<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;

    public $timestamps=false;
    protected $fillable = [
        'id_class_category',
        'nama',
        'deskripsi',
        'url_web',
        'web_id',
        'url_mobile',
        'mobile_id',
        'jml_video',
        'jml_kuis',
        'status_tersedia',
        'uuid',
        'urutan',
    ];

    public function class_category()
    {
        return $this->belongsTo(ClassesCategory::class,'id_class_category','id');
    }

    public function teacher()
    {
        return $this->hasMany(Teacher::class,'id_class','id');
    }

    public function content()
    {
        return $this->hasMany(Content::class,'id_class','id');
    }
}
