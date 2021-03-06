<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoSession extends Model
{
    use HasFactory;
    protected $table = 'video_session';
    public $timestamps=false;
    protected $fillable = [
        'key',
        'value',
    ];
}
