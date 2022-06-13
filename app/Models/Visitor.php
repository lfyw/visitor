<?php

namespace App\Models;

use AlicFeng\IdentityCard\Application\IdentityCard;
use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Lfyw\FileManager\Traits\HasFiles;

class Visitor extends Model
{
    use HasFactory, HasFiles;

    const USER = 1;
    const TEMPORARY = 2;

    protected $guarded = [];

    public function ways():BelongsToMany
    {
        return $this->belongsToMany(Way::class);
    }

    public function visitorType():BelongsTo
    {
        return $this->belongsTo(VisitorType::class);
    }

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userAsVisitor():BelongsTo
    {
        return $this->belongsTo(User::class, 'id_card', 'id_card');
    }

    public function scopeFromUser(Builder $builder): Builder
    {
        return $builder->where('type', Visitor::USER);
    }

    public function scopeFromTemporary(Builder $builder):Builder
    {
        return $builder->where('type', Visitor::TEMPORARY);
    }

    public function scopeWhenName(Builder $builder, $name):Builder
    {
        return $builder->when(filled($name) ,fn(Builder $visitor) => $visitor->where('name', 'like', "%{$name}%"));
    }

    public function scopeWhenIdCard(Builder $builder, $idCard):Builder
    {
        return $builder->when(filled($idCard), fn(Builder $visitor) => $visitor->where('id_card', 'like', "%{$idCard}%"));
    }

    public function scopeWhenPhone(Builder $builder, $phone):Builder
    {
        return $builder->when(filled($phone), fn(Builder $visitor) => $visitor->where('phone', $phone));
    }

    public function scopeWhenVisitorTypeId(Builder $builder, $visitorTypeId):Builder
    {
        return $builder->when(filled($visitorTypeId), fn(Builder $visitor) => $visitor->where('visitor_type_id', $visitorTypeId));
    }

    public function scopeWhenAgeFrom(Builder $builder, $ageFrom):Builder
    {
        return $builder->when(filled($ageFrom), fn(Builder $visitor) => $visitor->where('age', '>=', $ageFrom));
    }

    public function scopeWhenAgeTo(Builder $builder, $ageTo):Builder
    {
        return $builder->when(filled($ageTo), fn(Builder $visitor) => $visitor->where('age', '<=', $ageTo));
    }

    public function scopeWhenWayId(Builder $builder, $wayId):Builder
    {
        return $builder->when(filled($wayId), fn(Builder $visitor) => $visitor->whereHas('ways', fn(Builder $way) => $way->where('id', $wayId)));
    }

    public function scopeWhenAccessDateFrom(Builder $builder, $accessDateFrom):Builder
    {
        return $builder->when(filled($accessDateFrom), fn(Builder $visitor) => $visitor->whereDate('access_date_from', '>=', $accessDateFrom));
    }

    public function scopeWhenAccessDateTo(Builder $builder, $accessDateTo):Builder
    {
        return $builder->when(filled($accessDateTo), fn(Builder $visitor) => $visitor->whereDate('access_date_to', '<=', $accessDateTo));
    }

    public function scopeNotInBlacklist(Builder $builder)
    {
        return $builder->where('is_in_blacklist', false);
    }

    public function scopeCanSee(Builder $builder)
    {
        /**@var User $user * */
        $user = auth()->user();
        if ($user->hasRoles([RoleEnum::ADMIN, RoleEnum::SYSTEM_ADMIN])) {
            return $builder;
        } elseif ($user->hasRole(RoleEnum::EMPLOYEE)) {
            return $builder->where('user_id', $user->id);
        }elseif ($user->hasRole(RoleEnum::DEPARTMENT_ADMIN)){
            //当是部门管理员时，以该人员所属部门为权限，可以查看该部门下所有人的临时访客申请记录
            return $builder->whereHas('user',fn(Builder $builder) => $builder->where('department_id', $user->department_id));
        }
        return $builder->where('user_id', 0);
    }

    public function blockBlacklist()
    {
        return $this->fill(['is_in_blacklist' => true])->save();
    }

    public function cancelBlacklist()
    {
        return $this->fill(['is_in_blacklist' => false])->save();
    }

    public function getType()
    {
        return $this->type == Visitor::TEMPORARY
            ? $this->visitorType->name
            : $this->userAsVisitor->userType->name;
    }

    public function getUserDepartment()
    {
        $userDepartment = '';
        if ($this->type == Visitor::TEMPORARY) {
            $department = $this->user->department;
            $userDepartment = $department->getAncestors(['name'])->pluck('name')->push($department->name)->implode('-');
        }
        return $userDepartment;
    }

    public function getUserName()
    {
        return $this->type == Visitor::TEMPORARY ? $this->user->real_name : null;
    }
}
