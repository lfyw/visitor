<?php

namespace App\Http\Requests\Pc;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VisitorTypeRequest extends FormRequest
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
                'name' => ['required', 'unique:visitor_types', 'max:64'],
                'note' => ['nullable', 'max:256']
            ],
            'PUT' => [
                'name' => ['required', Rule::unique('visitor_types', 'name')->ignore($this->visitorType)],
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
