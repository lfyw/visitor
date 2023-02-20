<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;

class UserExport implements FromArray
{
    use Exportable;

    protected $searchBuilder;

    public function __construct()
    {
        $this->searchBuilder = User::latest('id');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        $data[] = [
            '姓名', '用户名', '部门', '人员类型', '角色', '人员状态', '职务', '身份证号', '手机号','路线'
        ];
        $users = $this->searchBuilder->with([
            'userType',
            'role',
            'ways',
        ])->get();
        foreach ($users as $user){
            $data[] = [
                $user->real_name,
                $user->name,
                $this->getUserDepartment($user),
                $user->userType?->name,
                $user->role?->name,
                $user->user_status,
                $user->duty,
                "'" . sm4decrypt($user->id_card),
                "'" . sm4decrypt($user->phone_number),
                implode(',', $user->ways?->pluck('name')->toArray())
            ];
        }
        return $data;
    }

    public function searcher(array $searchers)
    {
        $this->searchBuilder->whenRealName($searchers['real_name'] ?? null)
            ->whenRoleId($searchers['role_id'] ?? null)
            ->whenUserStatus($searchers['user_status'] ?? null)
            ->whenDepartmentId($searchers['department_id'] ?? null)
            ->whenIdCard($searchers['id_card'] ?? null)
            ->whenPhoneNumber($searchers['phone_number'] ?? null)
            ->adminAlwaysBeHidden()
            ->canSee()
            ->when($searchers['ids'] ?? null, fn(Builder $builder) => $builder->whereIn('id', $searchers['ids']));
        return $this;
    }

    protected function getUserDepartment($user)
    {
        $department = $user->department;
        $userDepartment = $department->getAncestors(['name'])->pluck('name')->push($department->name)->implode('-');
        return $userDepartment;
    }
}
