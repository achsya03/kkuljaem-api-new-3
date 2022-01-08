<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Banner extends Model
{
    use Notifiable;

    public function getIncrementing()
    {
        return false;
    }

    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }

    public $timestamps=false;
    protected $fillable = [
        'judul_banner',
        'url_web',
        'web_id',
        'url_mobile',
        'mobile_id',
        'deskripsi',
        'label',
        'link',
        'uuid',
    ];



}
