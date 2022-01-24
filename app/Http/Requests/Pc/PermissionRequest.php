<?php

namespace App\Http\Requests\Pc;

use Illuminate\Foundation\Http\FormRequest;

class PermissionRequest extends FormRequest
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
        return [
            'name' => ['required', 'max:64'],
            'type' => ['required', 'max:64'],
            'route' => ['required', 'max:64'],
            'note' => ['nullable', 'max:256'],
            'parent_id' => ['nullable', 'exists:permissions,id']
        ];
    }

    public function attributes()
    {
        return [
            'name' => '权限名称',
            'type' => '权限类型',
            'route' => '路由',
            'note' => '备注',
            'parent_id' => '上级id'
        ];
    }
}
