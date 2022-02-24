<?php

namespace App\Http\Requests\Pc;

use Illuminate\Foundation\Http\FormRequest;
use AlicFeng\IdentityCard\InfoHelper;

class VisitorRequest extends FormRequest
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
        return match($this->method){
            'POST' => [
                'name' => ['required'],
                'visitor_type_id' => ['required', 'exists:visitor_types,id'],
                'id_card' => ['required', function($attribute, $value, $fail){
                    if(!InfoHelper::identityCard()->validate($value)){
                        $fail('身份证号格式错误');
                    }
                }],
                'phone' => ['required'],
                'unit' => ['required'],
                'reason' => ['nullable'],
                'relation' => ['nullable'],
                'user_id' => ['required'],
                'limiter' => ['required', 'integer'],
                'access_date' => ['required', 'date'],
                'access_time' => ['array', 'required'],
                'access_time.*' => ['required'],
                'face_picture_ids' => ['required', 'array'],
                'face_picture_ids.*' => ['required', 'exists:files,id'],
                'way_ids' => ['required', 'array'],
                'way_ids.*' => ['required', 'exists:ways,id'],

            ],
            'PUT' => [
                'name' => ['required'],
                'visitor_type_id' => ['required', 'exists:visitor_types,id'],
                'id_card' => ['required', function($attribute, $value, $fail){
                    if(!InfoHelper::identityCard()->validate($value)){
                        $fail('身份证号格式错误');
                    }
                }],
                'phone' => ['required'],
                'unit' => ['required'],
                'reason' => ['nullable'],
                'relation' => ['nullable'],
                'user_id' => ['required'],
                'limiter' => ['required', 'integer'],
                'access_date' => ['required', 'date'],
                'access_time' => ['array', 'required'],
                'access_time.*' => ['required'],
                'face_picture_ids' => ['required', 'array'],
                'face_picture_ids.*' => ['required', 'exists:files,id'],
                'way_ids' => ['required', 'array'],
                'way_ids.*' => ['required', 'exists:ways,id'],

            ],
            'DELETE' => [
                'ids' => ['required', 'array'],
                'ids.*' => ['required', 'exists:visitors,id']
            ],
            default => [],
        };
    }

    public function attibutes()
    {
        return [
            'name' => '访客姓名',
            'visitor_type_id' => '访客类型id',
            'id_card' => '身份证号',
            'phone' => '手机号',
            'unit' => '所属单位',
            'reason' => '访问事由',
            'relation' => '关系',
            'face_picture_ids.*' => '人脸照片',
            'face_picture_ids' => '人脸照片',
            'user_id' => '被访问者id',
            'limiter' => '访问次数',
            'way_ids' => '访问路线',
            'way_ids.*' => '访问路线',
            'access_date' => '访问日期',
            'access_time' => '访问时间',
            'ids' => '访客id',
            'ids.*' => '访客id'
        ];
    }
}
