<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoTheme extends Model
{
    use HasFactory;
    protected $table = 'video_theme';
    public $timestamps=false;
    protected $fillable = [
        'id_video',
        'id_theme',
        'uuid',
    ];

    public function video()
    {
        return $this->belongsTo(Video::class,'id_video','id');
    }
    public function theme()
    {
        return $this->belongsTo(Theme::class,'id_theme','id');
    }
}
