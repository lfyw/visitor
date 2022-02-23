<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class VisitorSetting extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [
        'approver' => 'array'
    ];

    public function ways():BelongsToMany
    {
        return $this->belongsToMany(Way::class, 'visitor_setting_way');
    }

    public function visitorType():BelongsTo
    {
        return $this->belongsTo(VisitorType::class);
    }
}
