<?php

namespace App\Http\Requests\Pc;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
            'POST', 'PUT' => [
                'name' => ['required'],
                'permission_ids' => ['nullable', 'array'],
                'permission_ids.*' => ['nullable', 'exists:permissions,id'],
            ],
            'DELETE' => [
                'ids' => ['required', 'array'],
                'ids.*' => ['required', 'exists:roles,id']
            ],
            default => [],
        };
    }

    public function attributes()
    {
        return [
            'name' => '角色名称',
            'permission_ids' => '权限id',
            'permission_ids.*' => '权限id',
            'ids' => '角色id',
            'ids.*' => '角色id'
        ];
    }
}
