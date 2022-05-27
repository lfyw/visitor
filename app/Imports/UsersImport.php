<?php

namespace App\Imports;

use AlicFeng\IdentityCard\Application\IdentityCard;
use App\Exceptions\ImportValidateException;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use App\Models\UserType;
use App\Models\Way;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;

class UsersImport implements ToCollection
{
    use Importable;

    protected $errors = [];


    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $rows = $collection->skip(3);
        $this->setRowsCount($rows->count());

        $rows->each(function ($row) {
            try {
                $format = $this->format($row);
                $user = User::create($format['formatRow']);
                $user->ways()->sync($format['wayIds']);
            } catch (ImportValidateException $exception) {
                $this->formatNumberToString($row);
                $this->pushError($row, $exception->getMessage());
                return;
            } catch (\Exception $exception) {
                $this->formatNumberToString($row);
                \Log::error('导入人员异常:' . $exception->getMessage());
                $this->pushError($row, '导入异常，请联系系统管理员处理');
                return;
            }
        });
    }

    protected function format($row)
    {
        $formatRow['name'] = $this->validateName($row[0]);
        $formatRow['real_name'] = $this->validateRealName($row[1]);
        $formatRow['department_id'] = $this->validateDepartmentId($row[2]);
        $formatRow['user_type_id'] = $this->validateUserTypeId($row[3]);
        $formatRow['role_id'] = $this->validateRoleId($row[4]);
        $formatRow['user_status'] = $this->validateUserStatus($row[5]);
        $formatRow['duty'] = $row[6];
        $formatRow['id_card'] = $this->validateIdCard($row[7]);
        $formatRow['phone_number'] = $this->validatePhoneNumber($row[8]);
        $formatRow['password'] = bcrypt(Str::substr($formatRow['id_card'], -6, 6));
        $wayIds = $this->validateWayIds($row[9]);

        return compact('formatRow', 'wayIds');
    }

    protected function formatNumberToString($row)
    {
        $row[7] = "'" . $row[7];
        return $row;
    }

    protected function validateWayIds($ways)
    {
        $wayIds = [];
        $ways = explode('，', $ways);
        foreach ($ways as $way) {
            throw_unless($wayModel = Way::firstWhere('name', $way), new ImportValidateException('路线名称在系统中不存在'));
            array_push($wayIds, $wayModel->id);
        }
        return array_values(array_unique($wayIds));
    }

    protected function validatePhoneNumber($phoneNumber)
    {
        throw_unless($phoneNumber, new ImportValidateException('手机号不能为空'));
        return $phoneNumber;
    }

    protected function validateIdCard($idCard)
    {
        throw_unless($idCard, new ImportValidateException('身份证号不能为空'));
        throw_unless((new IdentityCard())->validate($idCard), new ImportValidateException('身份证号规则错误'));
        $idCard = Str::upper($idCard);
        throw_if(User::firstWhere('id_card', $idCard), new ImportValidateException('身份证号已存在系统中'));
        return $idCard;
    }

    protected function validateUserStatus($userStatus)
    {
        throw_unless($userStatus, new ImportValidateException('在职状态不能为空'));
        throw_unless(in_array($userStatus, ['在职', '离职']), new ImportValidateException('在职状态当前仅可选：在职、离职'));
        return $userStatus;
    }

    protected function validateRoleId($role)
    {
        throw_unless($role, new ImportValidateException('角色不能为空'));
        throw_unless($roleModel = Role::firstWhere('name', $role), new ImportValidateException('角色在系统中不存在'));
        return $roleModel->id;
    }

    protected function validateUserTypeId($userType)
    {
        throw_unless($userType, new ImportValidateException('人员类型不能为空'));
        throw_unless($userTypeModel = UserType::firstWhere('name', $userType), new ImportValidateException('人员类型在系统中不存在'));
        return $userTypeModel->id;
    }

    protected function validateName($name)
    {
        throw_unless($name, new ImportValidateException('用户名不能为空'));
        throw_if(User::where('name', $name)->exists(), new ImportValidateException('用户名已存在'));
        return $name;
    }

    protected function validateRealName($realName)
    {
        throw_unless($realName, new ImportValidateException('姓名不能为空'));
        return $realName;
    }

    protected function validateDepartmentId($department)
    {
        throw_unless($department, new ImportValidateException('部门/科室不能为空'));
        throw_unless($departmentModel = Department::where('name', $department)->first(), new ImportValidateException('部门/科室名称在系统中不存在'));
        throw_if(Department::where('name', $department)->count() > 1, new ImportValidateException('部门/科室名称在系统中有重复'));
        return $departmentModel->id;
    }

    protected function setRowsCount($count)
    {
        $this->rowsCount = $count;
    }

    public function getRowsCount(): int
    {
        return $this->rowsCount;
    }

    public function getErrorsCount(): int
    {
        return count($this->errors) ?: 0;
    }

    public function getErrorsWithHeader(): array
    {
        $header = $this->getHeaders();
        return $header ? array_merge([$header], $this->errors) : $this->errors;
    }


    protected function getHeaders(): array
    {
        return ['用户名', '姓名', '所属部门/科室', '人员类型', '角色', '在职状态', '职务', '身份证号', '手机号', '通行路线'];
    }

    protected function pushError($row, $message = ''): array
    {
        $error = $row->toArray();
        array_push($error, $message);
        array_push($this->errors, $error);
        return $error;
    }
}
