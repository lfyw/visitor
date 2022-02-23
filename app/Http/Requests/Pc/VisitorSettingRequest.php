<?php

namespace App\Http\Requests\Pc;

use App\Enums\ApplyPeriod;
use App\Enums\ApproverType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Lfyw\LfywEnum\Rules\EnumValue;

class VisitorSettingRequest extends FormRequest
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
            'POST' => [
                'visitor_type_id' => ['required', 'unique:visitor_settings,visitor_type_id'],
                'way_ids' => ['required', 'array'],
                'way_ids.*' => ['required', 'exists:ways,id'],
                'apply_period' => ['required', new EnumValue(ApplyPeriod::class)],
                'approver' => ['required', 'array'],
                'approver.*.type' => ['required', new EnumValue(ApproverType::class)],
                'approver.*.order' => ['required', 'integer'],
                'approver.*.role_id' => ['required_if:approver.*.type,' . ApproverType::ROLE->value, 'exists:roles,id'],
                'visitor_limiter' => ['required', 'integer'],
                'visitor_relation' => ['required', 'boolean'],
            ],
            'PUT' => [
                'visitor_type_id' => ['required', Rule::unique('visitor_settings')->ignore($this->visitor_setting)],
                'way_ids' => ['required', 'array'],
                'way_ids.*' => ['required', 'exists:ways,id'],
                'apply_period' => ['required', new EnumValue(ApplyPeriod::class)],
                'approver' => ['required', 'array'],
                'approver.*.type' => ['required', new EnumValue(ApproverType::class)],
                'approver.*.order' => ['required', 'integer'],
                'approver.*.role_id' => ['required_if:approver.*.type,' . ApproverType::ROLE->value, 'exists:roles,id'],
                'visitor_limiter' => ['required', 'integer'],
                'visitor_relation' => ['required', 'boolean'],
            ],
            default => [],
        };
    }

    public function attributes()
    {
        return [
            'visitor_type_id' => '访客类型',
            'way_ids' => '可申请访问线路',
            'way_ids.*' => '可申请访问线路',
            'apply_period' => '审批有效时限',
            'approver' => '审批人设置',
            'approver.*.type' => '审批人类型',
            'approver.*.order' => '审批顺序',
            'approver.*.role_id' => '审批角色id',
            'visitor_limiter' => '访客人数限制',
            'visitor_relation' => '是否开启访客关系选择'
        ];
    }
}
