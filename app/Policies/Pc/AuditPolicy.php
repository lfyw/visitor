<?php

namespace App\Policies\Pc;

use App\Models\Audit;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class AuditPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, Audit $audit)
    {
        return in_array($user->id, $audit->auditors()->pluck('user_id')->toArray());
    }

    public function before(User $user)
    {
        if ($user->role?->name == Role::SUPER_ADMIN){
            return true;
        }
    }
}
