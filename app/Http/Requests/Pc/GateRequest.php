<?php

namespace App\Http\Requests\Pc;

use Illuminate\Foundation\Http\FormRequest;
use App\Supports\Enums\GateRule;
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
        dump(GateRule::IN->getName());
        return [
            'number' => ['required', 'unique:gates', 'max:32'],
            'type' => ['required', 'max:32'],
            'location' => ['required', 'max:128'],
            'rule' => ['required', Rule::in(GateRule::getValues())],
            'note' => ['nullable', 'max:256']
        ];
    }

    public function attributes()
    {
        return [
            'number' => '闸机编号',
            'type' => '闸机型号',
            'location' => '闸机位置',
            'rule' => '进出规则',
            'note' => '备注'
        ];
    }
}
