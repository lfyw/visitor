<?php

namespace App\Http\Requests\Api;

use App\Enums\AuditStatus;
use App\Models\Audit;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

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
            'name' => 'required',
            'id_card' => ['required', function($attribute, $value, $fail){
            if (Audit::where('id_card', $value)->whereAuditStatus(AuditStatus::WAITING)->first()){
                return $fail('正在审核中，请勿重新提交');
            }
            if (User::firstWhere('id_card', sm4encrypt($value))){
                return $fail('正式员工无需通过临时访客申请');
            }
            }],
            'phone' => 'required',
            'unit' => 'nullable',
            'user_id' => ['required', 'exists:users,id'],
            'visitor_type_id' => ['required', 'exists:visitor_types,id'],
            'way_ids' => ['required', 'array'],
            'way_ids.*' => ['required', 'exists:ways,id'],
            'access_date_from' => ['required'],
            'access_date_to' => ['required'],
            'reason' => 'nullable',
            'relation' => 'nullable',
            'face_picture_ids' => ['required', 'array'],
            'face_picture_ids.*' => ['required', 'exists:files,id']
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
            'relation' => '关系',
            'face_picture_ids' => '面容照片',
            'face_picture_ids.*' => '面容照片'
        ];
    }
}
