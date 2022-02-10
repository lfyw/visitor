<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileRequest extends FormRequest
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
            'file' => ['required', 'file'],
            'keep_origin_name' => ['nullable', 'boolean'],
            'guess_extension' => ['nullable', 'boolean']
        ];
    }

    public function attributes()
    {
        return [
            'file' => '文件',
            'keep_origin_name' => '是否保存原文件名',
            'guess_extension' => '是否自动识别扩展名'
        ];
    }
}
