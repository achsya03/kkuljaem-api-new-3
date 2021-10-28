<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KkuljaemInfo extends Model
{
    use HasFactory;
    protected $table = 'kkuljaem_info';
    public $timestamps=false;
    protected $fillable = [
        'key',
        'value',
    ];
}
