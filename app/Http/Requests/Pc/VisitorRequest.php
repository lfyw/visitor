<?php

namespace App\Http\Requests\Pc;

use AlicFeng\IdentityCard\InfoHelper;
use App\Models\Blacklist;
use App\Models\Visitor;
use App\Models\VisitorSetting;
use App\Models\VisitorType;
use App\Rules\IdCard;
use Illuminate\Foundation\Http\FormRequest;

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
        return match ($this->method()) {
            'POST' => [
                'name' => ['required'],
                'visitor_type_id' => ['required', 'exists:visitor_types,id'],
                'id_card' => ['required', new IdCard(), function ($attribute, $value, $fail) {
                    if (Blacklist::idCard($value)->exists()) {
                        return $fail('已经存在于黑名单中');
                    }
                }],
                'phone' => ['required'],
                'unit' => ['required'],
                'reason' => ['nullable'],
                'relation' => ['nullable'],
                'user_id' => ['required', 'exists:users,id', function ($attribute, $value, $fail) {
                    //如果有亲属关系;判断被访问者亲属是不是超过了配置值
                    if ($this->relation) {
                        $limiter = VisitorSetting::firstWhere('visitor_type_id', $this->visitor_type_id)?->visitor_limiter;
                        if ($limiter) {
                            $visitorTypeVisitorCount = Visitor::whereUserId($this->user_id)->where('visitor_type_id', $this->visitor_type_id)->count();
                            if ($visitorTypeVisitorCount > $limiter) {
                                return $fail('访客人数超过' . $limiter . '次,已达到上限');
                            }
                        }
                    }
                }],
                'limiter' => ['required', 'integer'],
                'access_date_from' => ['required', 'date'],
                'access_date_to' => ['required', 'date'],
                'access_time_from' => ['required'],
                'access_time_to' => ['required'],
                'face_picture_ids' => ['required', 'array'],
                'face_picture_ids.*' => ['required', 'exists:files,id'],
                'way_ids' => ['required', 'array'],
                'way_ids.*' => ['required', 'exists:ways,id'],

            ],
            'PUT' => [
                'name' => ['required'],
                'visitor_type_id' => ['required', 'exists:visitor_types,id'],
                'id_card' => ['required', new IdCard(), function ($attribute, $value, $fail) {
                    if (Blacklist::idCard($value)->exists()) {
                        return $fail('已经存在于黑名单中');
                    }
                }],
                'phone' => ['required'],
                'unit' => ['required'],
                'reason' => ['nullable'],
                'relation' => ['nullable'],
                'user_id' => ['required', function ($attribute, $value, $fail) {
                    //如果有亲属关系;判断被访问者亲属是不是超过了配置值
                    if ($this->relation) {
                        $limiter = VisitorSetting::firstWhere('visitor_type_id', $this->visitor_type_id)?->visitor_limiter;
                        if ($limiter) {
                            $visitorTypeVisitorCount = Visitor::whereUserId($this->user_id)->where('visitor_type_id', $this->visitor_type_id)->count();
                            if ($visitorTypeVisitorCount > $limiter) {
                                return $fail('访客人数超过' . $limiter . '次,已达到上限');
                            }
                        }
                    }
                }],
                'limiter' => ['required', 'integer'],
                'access_date_from' => ['required', 'date'],
                'access_date_to' => ['required', 'date'],
                'access_time_from' => ['required'],
                'access_time_to' => ['required'],
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

    public function attributes()
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
            'access_date_from' => '起始访问日期',
            'access_date_to' => '结束访问日期',
            'access_time_from' => '起始访问时间',
            'access_time_to' => '结束访问时间',
            'ids' => '访客id',
            'ids.*' => '访客id'
        ];
    }
}
