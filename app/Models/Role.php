<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

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

    public function user():BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
