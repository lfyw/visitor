<?php

namespace App\Models;

use App\Enums\RoleEnum;
use App\Traits\HasAuth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Lfyw\FileManager\Traits\HasFiles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasFiles, HasAuth;

    const SUPER_ADMIN = 'admin';

    protected $guarded = [];

    protected $hidden = [
        'password',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function userType(): BelongsTo
    {
        return $this->belongsTo(UserType::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function ways(): BelongsToMany
    {
        return $this->belongsToMany(Way::class);
    }

    public function permissions():BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    public function scopeWhenRealName(Builder $builder, $realName): Builder
    {
        return $builder->when(filled($realName), fn(Builder $builder) => $builder->where('real_name', 'like', "%{$realName}%"));
    }

    public function scopeWhenRoleId(Builder $builder, $roleId): Builder
    {
        return $builder->when(filled($roleId), fn(Builder $builder) => $builder->where('role_id', $roleId));
    }

    public function scopeWhenUserStatus(Builder $builder, $userStatus): Builder
    {
        return $builder->when(filled($userStatus), fn(Builder $builder) => $builder->where('user_status', $userStatus));
    }

    public function scopeWhenDepartmentId(Builder $builder, $departmentId): Builder
    {
        return $builder->when(filled($departmentId), function ($query) use ($departmentId) {
            $descendants = Department::descendantsAndSelf($departmentId)->pluck('id');
            return $query->whereIn('department_id', $descendants);
        });
    }

    public function scopeWhenIdCard(Builder $builder, $idCard):Builder
    {
        return $builder->when($idCard, fn(Builder $builder) => $builder->where('id_card', $idCard));
    }

    public function scopeWhenPhoneNumber(Builder $builder, $phoneNumber):Builder
    {
        return $builder->when($phoneNumber, fn(Builder $builder) => $builder->where('phone_number', $phoneNumber));
    }

    public function scopeAdminShouldBeHidden(Builder $builder, $user): Builder
    {
        return $builder->when($user->name !== User::SUPER_ADMIN,fn(Builder $user) => $user->where('name', '<>', User::SUPER_ADMIN));
    }

    public function scopeAdminAlwaysBeHidden(Builder $builder): Builder
    {
        return $builder->where('name', '<>', User::SUPER_ADMIN);
    }

    public function scopeCanSee(Builder $builder)
    {
        /**@var User $user * */
        $user = auth()->user();
        if ($user->hasRoles([RoleEnum::ADMIN, RoleEnum::SYSTEM_ADMIN])) {
            return $builder;
        } elseif ($user->hasRole(RoleEnum::EMPLOYEE)) {
            return $builder->where('id', $user->id);
        }elseif ($user->hasRole(RoleEnum::DEPARTMENT_ADMIN)){
            //当是部门管理员时，以该人员所属部门为权限，可以查看该部门下所有人的临时访客申请记录
            return $builder->where('department_id', $user->department_id);
        }
        return $builder->where('user_id', 0);
    }
}
