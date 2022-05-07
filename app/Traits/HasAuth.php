<?php

namespace App\Traits;

/**
 * 对于超级管理员和系统管理员都可以看到全部数据。系统实际只有内部员工与部门管理员，两者判断数据权限依靠的是被访问人字段。
 */
trait HasAuth
{
    public function hasRoles(array $roles): bool
    {
        foreach ($roles as $role) {
            if (!$this->hasRole($role)) {
                return false;
            }
        }
        return true;
    }

    public function hasRole($role): bool
    {
        return $this->role()->where('name', $role)->exists();
    }
}
