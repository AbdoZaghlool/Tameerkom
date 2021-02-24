<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\AdminResetPasswordNotification;
use Laratrust\Traits\LaratrustUserTrait;

class Admin extends Authenticatable
{
    use Notifiable, LaratrustUserTrait;
    protected $guard = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','phone',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AdminResetPasswordNotification($token));
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'admin_role');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'admin_permission');
    }
}