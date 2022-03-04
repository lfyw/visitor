<?php

namespace App\Http\Requests\Pc;

use Illuminate\Foundation\Http\FormRequest;
use Lfyw\LfywEnum\Rules\EnumValue;
use App\Enums\GateRule;
use Illuminate\Validation\Rule;

class GateRequest extends FormRequest
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
                'number' => ['required', 'unique:gates', 'max:32'],
                'type' => ['required', 'max:32'],
                'ip' => ['nullable'],
                'location' => ['required', 'max:128'],
                'rule' => ['required', new EnumValue(GateRule::class)],
                'note' => ['nullable', 'max:256']
            ],
            'PUT' => [
                'number' => ['required', 'max:32', Rule::unique('gates')->ignore($this->gate)],
                'type' => ['required', 'max:32'],
                'location' => ['required', 'max:128'],
                'ip' => ['nullable'],
                'rule' => ['required', new EnumValue(GateRule::class)],
                'note' => ['nullable', 'max:256']
            ],
            'DELETE' => [
                'ids' => 'required|array',
                'ids.*' => 'required|exists:gates,id'
            ],
            default => [],
        };
    }

    public function attributes()
    {
        return [
            'number' => '闸机编号',
            'type' => '闸机型号',
            'location' => '闸机位置',
            'rule' => '进出规则',
            'note' => '备注',
            'ip' => 'ip地址'
        ];
    }
}
