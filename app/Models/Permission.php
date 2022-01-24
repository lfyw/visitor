<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Permission extends Model
{
    use HasFactory, NodeTrait;

    public $timestamps = false;

    protected $guarded = [];

    protected $hidden = ['_lft', '_rgt'];
}
