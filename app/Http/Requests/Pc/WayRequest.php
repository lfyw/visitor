<?php

namespace App\Http\Requests\Pc;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WayRequest extends FormRequest
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
                'name' => ['required', 'unique:ways', 'max:128'],
                'note' => ['nullable', 'max:256'],
                'passageway_ids' => ['array', 'required'],
                'passageway_ids.*' => ['required', 'exists:passageways,id'],
            ],
            'PUT' => [
                'name' => ['required', 'max:128', Rule::unique('ways')->ignore($this->way)],
                'note' => ['nullable', 'max:256'],
                'passageway_ids' => ['array', 'required'],
                'passageway_ids.*' => ['required', 'exists:passageways,id'],
            ],
            'DELETE' => [
                'ids' => ['required', 'array'],
                'ids.*' => ['required', 'exists:ways,id']
            ],
            default => [],
        };
    }

    public function attributes()
    {
        return [
            'name' => '路线名称',
            'note' => '备注',
            'passageway_ids' => '相关通道',
            'passageway_ids.*' => '相关通道',
            'ids' => '路线',
            'ids.*' => '路线'
        ];
    }
}
