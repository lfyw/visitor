<?php

namespace App\Http\Requests\Pc;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PassagewayRequest extends FormRequest
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
                'name' => ['required', 'unique:passageways', 'max:128'],
                'note' => ['nullable', 'max:256'],
                'gate_ids' => ['required', 'array'],
                'gate_ids.*' => ['required', 'exists:gates,id']
            ],
            'PUT' => [
                'name' => ['required', 'max:128', Rule::unique('passageways')->ignore($this->passageway)],
                'note' => ['nullable', 'max:256'],
                'gate_ids' => ['required', 'array'],
                'gate_ids.*' => ['required', 'exists:gates,id']
            ],
            'DELETE' => [
                'ids' => ['required', 'array'],
                'ids.*' => ['required', 'exists:passageways,id']
            ],
            default => [],
        };
    }

    public function attributes()
    {
        return [
            'name' => '通道名称',
            'note' => '备注',
            'gate_ids' => '相关闸机',
            'gate_ids.*' => '相关闸机',
            'ids' => '通道id',
            'ids.*' => '通道'
        ];
    }
}
