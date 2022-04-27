<?php

namespace App\Http\Requests\Pc;

use App\Enums\WarningStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Lfyw\LfywEnum\Rules\EnumValue;

class WarningRequest extends FormRequest
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
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'exists:warnings,id'],
            'status' => ['required', new EnumValue(WarningStatus::class)],
            'note' => ['nullable']
        ];
    }

    public function attributes()
    {
        return [
            'status' => '处置结果',
            'note' => '处置情况描述',
            'ids' => '预警id',
            'ids.*' => '预警id'
        ];
    }
}
