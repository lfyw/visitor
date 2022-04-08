<?php

namespace App\Http\Requests\Pc;

use App\Enums\Template;
use Illuminate\Foundation\Http\FormRequest;
use Lfyw\LfywEnum\Rules\EnumValue;

class TemplateRequest extends FormRequest
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
            'template' => ['required', new EnumValue(Template::class)]
        ];
    }

    public function messages()
    {
        return [
            'template' => '模板名称'
        ];
    }
}
