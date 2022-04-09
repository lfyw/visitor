<?php

namespace App\Http\Requests\Pc;

use Illuminate\Foundation\Http\FormRequest;
use Lfyw\LfywEnum\Rules\EnumValue;
use App\Enums\UserStatus;
use App\Enums\IssueStatus;
use Illuminate\Validation\Rule;

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
        return match($this->method()){
            'POST' => [
                'name' => ['required', 'unique:users', 'string', 'max:32'],
                'real_name' => ['required', 'string', 'max:16'],
                'department_id' => ['nullable', 'exists:departments,id'],
                'user_type_id' => ['nullable', 'exists:user_types,id'],
                'role_id' => ['nullable', 'exists:roles,id'],
                'user_status' => ['nullable', new EnumValue(UserStatus::class)],
                'duty' => ['nullable'],
                'id_card' => ['required', 'unique:users', 'regex:/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/'],
                'phone_number' => ['required', 'unique:users'],
                'issue_status' => ['nullable', new EnumValue(IssueStatus::class)],
                'face_picture_ids' => ['required', 'array'],
                'face_picture_ids.*' => ['required', 'exists:files,id'],
                'way_ids' => ['required', 'array'],
                'way_ids.*' => ['required', 'exists:ways,id'],
            ],
            'PUT' => [
                'name' => ['required', 'string', 'max:32', Rule::unique('users', 'name')->ignore($this->user)],
                'real_name' => ['required', 'string', 'max:16'],
                'department_id' => ['nullable', 'exists:departments,id'],
                'user_type_id' => ['nullable', 'exists:user_types,id'],
                'role_id' => ['nullable', 'exists:roles,id'],
                'user_status' => ['nullable', new EnumValue(UserStatus::class)],
                'duty' => ['nullable'],
                'id_card' => ['required', 'regex:/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/', Rule::unique('users')->ignore($this->user)],
                'phone_number' => ['required', Rule::unique('users')->ignore($this->user)],
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
