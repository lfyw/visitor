<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VisitorType extends Model
{
    use HasFactory;

    const TEMPORARY = '临时访客';
    const FAMILY = '家属';



    protected $guarded = [];

    public $timestamps = false;

    public function visitorSettings():HasMany
    {
        return $this->hasMany(VisitorSetting::class);
    }

    public function visitors():HasMany
    {
        return $this->hasMany(Visitor::class);
    }
}
