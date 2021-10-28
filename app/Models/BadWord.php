<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BadWord extends Model
{
    use HasFactory;
    protected $table = 'bad_word';
    public $timestamps=false;
    protected $fillable = [
        'kata',
        'uuid',
    ];
}
