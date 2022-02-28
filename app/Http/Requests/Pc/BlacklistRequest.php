<?php

namespace App\Http\Requests\Pc;

use App\Rules\IdCard;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlacklistRequest extends FormRequest
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
            'POST' =>[
                'name' => ['required'],
                'id_card' => ['required', new IdCard(), 'unique:blacklists'],
                'phone' => ['required'],
                'reason' => ['nullable']
            ],
            'PUT' => [
                'name' => ['required'],
                'id_card' => ['required', new IdCard(), Rule::unique('blacklists')->ignore($this->blacklist)],
                'phone' => ['required'],
                'reason' => ['nullable']
            ],
            default => [],
        };
    }

    public function attributes()
    {
        return [
            'name' => '姓名',
            'id_card' => '身份证号',
            'phone' => '手机号',
            'reason' => '加入黑名单原因'
        ];
    }
}
