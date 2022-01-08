<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $table = 'notification';
    public $timestamps=false;
    protected $fillable = [
        'user_uuid',
        'judul',
        'keterangan',
        'posisi',
        'tgl_notif',
        'status',
        'uuid_target',
        'maker_uuid',
        'uuid',
    ];
}
