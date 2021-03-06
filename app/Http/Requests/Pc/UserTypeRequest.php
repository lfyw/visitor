<?php

namespace App\Http\Requests\Pc;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserTypeRequest extends FormRequest
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
                'name' => ['required', 'unique:user_types', 'max:64'],
                'note' => ['nullable', 'max:256']
            ],
            'PUT' => [
                'name' => ['required', Rule::unique('user_types', 'name')->ignore($this->userType)],
                'note' => ['nullable', 'max:256']
            ],
            default => [],
        };
    }

    public function attributes()
    {
        return [
            'name' => '访客类型名称',
            'note' => '备注'
        ];
    }
}
