<?php

namespace App\Http\Requests\Pc;

use App\Enums\Import;
use Illuminate\Foundation\Http\FormRequest;
use Lfyw\LfywEnum\Rules\EnumValue;

class ImportRequest extends FormRequest
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
            'excel' => ['required', 'file'],
            'import' => ['required', new EnumValue(Import::class)]
        ];
    }

    public function attributes()
    {
        return [
            'excel' => 'excel文件',
            'import' => '导入类型'
        ];
    }
}
