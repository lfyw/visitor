<?php

namespace App\Http\Requests\Pc;

use Illuminate\Foundation\Http\FormRequest;

class RuleRequest extends FormRequest
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
            'name' => ['required'],
            'value' => ['nullable', 'array'],
            'value.not_out' => ['sometimes', 'array'],
            'value.not_out.*.user_type_id' => ['sometimes', 'exists:user_types,id'],
            'value.not_out.*.duration' => ['sometimes', 'integer'],
            'value.scope' => ['sometimes', 'array'],
            'value.scope.*' => ['sometimes', 'exists:passageways,id'],
            'value.board' => ['sometimes', 'array'],
            'value.board.*' => ['sometimes', 'exists:passageways,id']
        ];
    }

    public function attributes()
    {
        return [
            'name' => '规则名称',
            'value' => '规则',
            'value.not_out' => '超时未出预警时长设置',
            'value.not_out.*.user_type' => '人员类型',
            'value.not_out.*.duration' => '单次进出时长不超过',
            'value.scope' => '预警范围设置',
            'value.scope.*' => '预警通道',
            'value.board' => '数据看板统计范围',
            'value.board.*' => '数据看板统计通道'
        ];
    }
}
