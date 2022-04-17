<?php

namespace App\Http\Requests\Pc;

use AlicFeng\IdentityCard\Application\IdentityCard;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FacePictureRequest extends FormRequest
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
            'type' => ['required', Rule::in(['visitor', 'user'])],
            'face_pictures' => ['required', 'array'],
            'face_pictures.*.id_card' => ['required'],
            'face_pictures.*.id' => ['required']
        ];
    }

    public function attributes()
    {
        return [
            'type' => '类型',
            'face_pictures' => '面容照片',
            'face_pictures.*.id_card' => '身份证号',
            'face_pictures.*.id' => '图片id'
        ];
    }
}
