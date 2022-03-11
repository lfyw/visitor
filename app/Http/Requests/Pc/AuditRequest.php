<?php

namespace App\Http\Requests\Pc;

use App\Enums\AuditStatus;
use Illuminate\Foundation\Http\FormRequest;
use Lfyw\LfywEnum\Rules\EnumValue;

class AuditRequest extends FormRequest
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
            'access_time_from' => ['nullable'],
            'access_time_to' => ['nullable'],
            'limiter' => ['nullable'],
            'audit_status' => ['required', new EnumValue(AuditStatus::class)],
            'refused_reason' => ['nullable']
        ];
    }

    public function attributes()
    {
        return [
            'access_time_from' => '访问时间起',
            'access_time_to' => '访问时间止',
            'limiter' => '访问次数',
            'audit_status' => '审核状态',
            'refused_reason' => '拒绝理由'
        ];
    }
}
