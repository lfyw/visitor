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
            'name' => ['required'],
            'visitor_type_id' => ['required', 'exists:visitor_types,id'],
            'id_card' => ['required'],
            'phone' => ['required'],
            'unit' => ['required'],
            'reason' => 'nullable',
            'way_ids' => ['array', 'required'],
            'way_ids.*' => ['required', 'exists:ways,id'],
            'access_date_from' => ['required'],
            'access_date_to' => ['required'],
            'face_picture_ids' => ['required', 'array'],
            'face_picture_ids.*' => ['required', 'exists:files,id'],
            'user_id' => ['required', 'exists:users,id'],
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
            'name' => '姓名',
            'id_card' => '身份证号',
            'phone' => '手机号',
            'unit' => '单位',
            'user_id' => '被访问者id',
            'visitor_type_id' => '访客类型id',
            'way_ids' => '访问路线',
            'way_ids.*' => '访问路线id',
            'access_date_from' => '起始访问日期',
            'access_date_to' => '结束访问日期',
            'reason' => '访问事由',
            'face_picture_ids' => '面容照片',
            'face_picture_ids.*' => '面容照片',
            'access_time_from' => '访问时间起',
            'access_time_to' => '访问时间止',
            'limiter' => '访问次数',
            'audit_status' => '审核状态',
            'refused_reason' => '拒绝理由'
        ];
    }
}
