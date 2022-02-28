<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Lfyw\FileManager\Traits\HasFiles;

class Audit extends Model
{
    use HasFactory, HasFiles;

    protected $guarded = [];

    public $casts = [
        'access_time' => 'array'
    ];

    public function ways():BelongsToMany
    {
        return $this->belongsToMany(Way::class);
    }

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function visitorType():BelongsTo
    {
        return $this->belongsTo(VisitorType::class);
    }
}
