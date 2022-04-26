<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    const SUPER_ADMIN = '超级管理员';
    const SYSTEM_ADMIN = '系统管理员';
//    const DEPART

    protected $guarded = [];

    public $timestamps = false;

    public static function booted()
    {
        static::deleted(function(Role $role){
            $role->permissions()->detach();
        });
    }

    public function permissions():BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function users():HasMany
    {
        return $this->hasMany(User::class);
    }
}
