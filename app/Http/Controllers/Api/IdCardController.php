<?php

namespace App\Http\Controllers\Api;

use AlicFeng\IdentityCard\Application\IdentityCard;
use AlicFeng\IdentityCard\InfoHelper;
use App\Http\Controllers\Controller;
use App\Models\Blacklist;
use Illuminate\Http\Response;

class IdCardController extends Controller
{
    public function valid()
    {
        if (!\request()->has('id_card')){
            return error('身份证号不能为空', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        if (!InfoHelper::identityCard()->validate(\request('id_card', ''))){
            return error('不是合法的身份证号', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        if ((new IdentityCard())->age(request('id_card')) > 60){
            return error('年龄超过60岁不允许登记', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        if (Blacklist::idCard(\request('id_card'))->exists()){
            return error('您已被纳入黑名单，无法进行访客登记', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        //返回访客信息
        return send_message('识别成功', Response::HTTP_OK);
    }
}
