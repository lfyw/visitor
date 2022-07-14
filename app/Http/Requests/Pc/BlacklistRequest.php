<?php

namespace App\Http\Requests\Pc;

use App\Models\Blacklist;
use App\Rules\IdCard;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
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
                'id_card' => ['required', new IdCard(), function($attribute, $value, $fail){
                    if (Blacklist::whereIdCard(sm4encrypt(Str::upper($value)))->exists()){
                        return $fail('该身份证号已被拉黑');
                    }
                }],
                'phone' => ['nullable'],
                'reason' => ['nullable']
            ],
            'PUT' => [
                'name' => ['required'],
                'id_card' => ['required', new IdCard(), function($attribute, $value, $fail){
                    if (Blacklist::whereIdCard(sm4encrypt(Str::upper($value)))->where('id', '<>', $this->blacklist->id)->exists()){
                        return $fail('该身份证号已被拉黑');
                    }
                }],
                'phone' => ['nullable'],
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
