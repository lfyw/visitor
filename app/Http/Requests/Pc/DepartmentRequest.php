<?php

namespace App\Http\Requests\Pc;

use App\Models\Department;
use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends FormRequest
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
                'address' => ['nullable', 'max:128'],
                'parent_id' => ['nullable']
            ],
            default => [],
        };
    }

    public function attributes()
    {
        return [
            'name' => '部门名称',
            'address' => '地址',
            'parent_id' => '上级部门',
        ];
    }
}
