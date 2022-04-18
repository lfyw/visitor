<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    const USER = '人员管理';
    const LOGIN = '登录';
    const DEPARTMENT = '部门管理';
    const BLACKLIST = '黑名单管理';
    const VISITOR = '访客管理';
    const GATE = '闸机管理';
    const PASSAGEWAY = '通道管理';
    const PERMISSION = '权限管理';
    const ROLE = '角色管理';
    const SETTING = '系统设置';
    const WAY = '路线管理';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeMakeAdminHidden(Builder $builder): Builder
    {
        $admin = User::firstWhere('name', User::SUPER_ADMIN);
        return $builder->where('user_id', '<>', $admin->id);
    }

    public function scopeWhenName(Builder $builder, $name): Builder
    {
        return $builder->when(filled($name), function (Builder $opeartionLog) use ($name) {
            $opeartionLog->whereHas('user', fn(Builder $user) => $user->where('name', 'like', "%{$name}%"));
        });
    }

    public function scopeWhenOperatedAtFrom(Builder $builder, $operatedAtFrom): Builder
    {
        return $builder->when(filled($operatedAtFrom), fn(Builder $operationLog) => $operationLog->where('operated_at', '>=', $operatedAtFrom));
    }

    public function scopeWhenOperatedAtTo(Builder $builder, $operatedAtTo): Builder
    {
        return $builder->when(filled($operatedAtTo), fn(Builder $operationLog) => $operationLog->where('operated_at', '<=', $operatedAtTo));
    }

    public function scopeWhenModule(Builder $builder, $module): Builder
    {
        return $builder->when(filled($module), fn(Builder $operationLog) => $operationLog->where('module', $module));
    }
}
