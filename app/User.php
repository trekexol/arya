<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $connection = 'logins';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles(){
        return $this->belongsTo('App\Permission\Models\Role','role_id');
    }

    public function company()
    {
        return $this->belongsTo('App\Company','id_company');
    }

    public function estado()
    {
        return $this->belongsTo('App\Estado','estado_id');
    }
    public function municipio()
    {
        return $this->belongsTo('App\Municipio','municipio_id');
    }
    public function branches()
    {
        return $this->belongsTo('App\Branch','id_branch');
    }
}
