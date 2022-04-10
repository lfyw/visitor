<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class PassingLogRequest extends FormRequest
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
            'id_card' => ['required'],
            'ip' => ['required'],
            'passed_at' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'id_card' => '身份证号',
            'ip' => 'ip地址',
            'passed_at' => '通行时间'
        ];
    }
}
