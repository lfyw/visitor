<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Kalnoy\Nestedset\NodeTrait;

class Permission extends Model
{
    use HasFactory, NodeTrait;

    public $timestamps = false;

    protected $guarded = [];

    protected $hidden = ['_lft', '_rgt'];

    public static function booted()
    {
        static::deleted(function(Permission $permission){
            $permission->roles()->detach();
        });
    }

    public function roles():BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
}
