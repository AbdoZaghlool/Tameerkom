<?php

namespace App;

use Laratrust\Models\LaratrustRole;

class Role extends LaratrustRole
{
    protected $fillable = ['name','display_name'];
    
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function admins()
    {
        return $this->belongsToMany(Admin::class);
    }
}
