<?php

namespace App\Models;

use App\Observers\BackupObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function booted()
    {
        static::observe(BackupObserver::class);
    }
}
