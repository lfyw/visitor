<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PassingLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function gate(): BelongsTo
    {
        return $this->belongsTo(Gate::class);
    }
}
