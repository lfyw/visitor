<?php

namespace App\Http\Requests\Api;

use AlicFeng\IdentityCard\Application\IdentityCard;
use App\Models\Issue;
use Illuminate\Foundation\Http\FormRequest;

class PassingLogRequest extends FormRequest
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
            'id_card' => ['required', function($attribute, $value, $fail){
                if (!(new IdentityCard())->validate($value)){
                    return $fail('身份证号格式错误');
                }
                if (Issue::where('id_card', $value)->doesntExist()){
                    return $fail('当前身份证尚未下发');
                }
            }],
            'ip' => ['required'],
            'passed_at' => ['required'],
            'snapshot' => ['nullable']
        ];
    }

    public function messages()
    {
        return [
            'id_card' => '身份证号',
            'ip' => 'ip地址',
            'passed_at' => '通行时间',
            'snapshot' => '快照'
        ];
    }
}
