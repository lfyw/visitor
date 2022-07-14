<?php

namespace App\Http\Requests\Pc;

use AlicFeng\IdentityCard\InfoHelper;
use App\Enums\IssueStatus;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Lfyw\LfywEnum\Rules\EnumValue;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return match ($this->method()) {
            'POST' => [
                'name' => ['required', 'unique:users', 'string', 'max:32'],
                'real_name' => ['required', 'string', 'max:16'],
                'department_id' => ['nullable', 'exists:departments,id'],
                'user_type_id' => ['nullable', 'exists:user_types,id'],
                'role_id' => ['nullable', 'exists:roles,id'],
                'user_status' => ['nullable', new EnumValue(UserStatus::class)],
                'duty' => ['nullable'],
                'id_card' => ['required', 'regex:/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/', function ($attribute, $value, $fail) {
                    if(!InfoHelper::identityCard()->validate($value)){
                        return $fail('身份证号不合法');
                    }
                    if (User::whereIdCard(sm4encrypt(Str::upper($value)))->exists()) {
                        return $fail('该身份证号已存在');
                    }
                    //判断如果时外协人员，年龄不能超过60岁,外协人员的 id 是3
                    if ($this->user_type_id == 3){
                        $age = InfoHelper::identityCard()->age($value);
                        if ($age > 60){
                            return $fail('外协人员年龄不得超过60岁');
                        }
                    }
                }],
                'phone_number' => ['required', function ($attribute, $value, $fail) {
                    if (User::wherePhoneNumber(sm4encrypt($value))->exists()) {
                        return $fail('该手机号已存在');
                    }
                }],
                'issue_status' => ['nullable', new EnumValue(IssueStatus::class)],
                'face_picture_ids' => ['required', 'array'],
                'face_picture_ids.*' => ['required', 'exists:files,id'],
                'way_ids' => ['required', 'array'],
                'way_ids.*' => ['required', 'exists:ways,id'],
            ],
            'PUT' => [
                'real_name' => ['required', 'string', 'max:16'],
                'department_id' => ['nullable', 'exists:departments,id'],
                'user_type_id' => ['nullable', 'exists:user_types,id'],
                'role_id' => ['nullable', 'exists:roles,id'],
                'user_status' => ['nullable', new EnumValue(UserStatus::class)],
                'duty' => ['nullable'],
                'id_card' => ['required', 'regex:/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/', function ($attribute, $value, $fail) {
                    if(!InfoHelper::identityCard()->validate($value)){
                        return $fail('身份证号不合法');
                    }
                    if (User::whereIdCard(sm4encrypt(Str::upper($value)))->where('id', '<>', $this->user->id)->exists()) {
                        return $fail('该身份证号已存在');
                    }
                    //判断如果时外协人员，年龄不能超过60岁,外协人员的 id 是3
                    if ($this->user_type_id == 3){
                        $age = InfoHelper::identityCard()->age($value);
                        if ($age > 60){
                            return $fail('外协人员年龄不得超过60岁');
                        }
                    }
                }],
                'phone_number' => ['required', function ($attribute, $value, $fail) {
                    if (User::wherePhoneNumber(sm4encrypt($value))->where('id', '<>', $this->user->id)->exists()) {
                        return $fail('该手机号已存在');
                    }
                }],
                'issue_status' => ['nullable', new EnumValue(IssueStatus::class)],
                'face_picture_ids' => ['required', 'array'],
                'face_picture_ids.*' => ['required', 'exists:files,id'],
                'way_ids' => ['required', 'array'],
                'way_ids.*' => ['required', 'exists:ways,id'],
                'password' => ['nullable', 'min:6']
            ],
            'DELETE' => [
                'ids' => ['required', 'array'],
                'ids.*' => ['required', 'exists:users,id']
            ],
            default => []
        };
    }

    public function attributes()
    {
        return [
            'name' => '登录用户名',
            'real_name' => '人员姓名',
            'department_id' => '所属部门',
            'user_type_id' => '人员类型',
            'role_id' => '所属角色',
            'user_status' => '用户状态',
            'duty' => '职务',
            'id_card' => '身份证号',
            'phone_number' => '手机号',
            'issue_status' => '下发状态',
            'face_picture_ids' => '人脸照片',
            'face_picture_ids.*' => '人脸照片',
            'way_ids' => '通行路线',
            'way_ids.*' => '通行路线',
            'ids' => '人员id',
            'ids.*' => '人员id',
        ];
    }
}
