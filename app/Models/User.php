<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps=false;
    protected $fillable = [
        'nama',
        'email',
        'password',
        'lokasi',
        'url_foto',
        'foto_id',
        'device_id',
        'status_aktif',
        'web_token',
        'tgl_langganan_akhir',
        'jenis_pengguna',
        'jenis_akun',
        'email_verified_at',
        'uuid',
    ];
    

    public function student()
    {
        return $this->hasMany(Student::class,'id_user','id');
    }

    public function detailMentor()
    {
        return $this->hasMany(DetailMentor::class,'id_users','id');
    }

    public function detailStudent()
    {
        return $this->hasMany(DetailStudent::class,'id_users','id');
    }

    public function subs()
    {
        return $this->hasMany(Subs::class,'id_user','id');
    }
    // public function comment()
    // {
    //     return $this->hasMany(Comment::class);
    // }
    // public function commentReport()
    // {
    //     return $this->hasMany(CommentReport::class);
    // }
    // public function postReport()
    // {
    //     return $this->hasMany(PostReport::class);
    // }
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'token_web',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

        // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
