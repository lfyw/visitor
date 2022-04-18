<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Http\Resources\Pc\IssueResource;
use App\Jobs\PushUser;
use App\Jobs\PushVisitor;
use App\Models\Issue;
use App\Models\User;
use App\Models\Visitor;
use App\Supports\Sdks\VisitorIssue;
use Illuminate\Http\Response;

class IssueController extends Controller
{
    public function index()
    {
        return IssueResource::collection(Issue::filterByIdCard(\request('id_card'))->with(['gate.passageways'])->latest('id')->paginate(\request('pageSize', 10)));
    }

    public function update(Issue $issue)
    {
        try {
            //下发请求
            VisitorIssue::addByIdCard($issue->id_card, $issue->gate->toArray());
            //成功则记录下发成功记录
            $issue->fill(['issue_status' => true])->save();
            Issue::syncIssue($issue->id_card);
            return no_content();
        } catch (\Exception $exception) {
            \Log::error('下发异常:' . $exception->getMessage());
            Issue::syncIssue($issue->id_card);
            return send_message('网络异常，请稍后重试', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteUser()
    {
        $this->validate(\request(), [
            'id_card' => 'required', 'exists:visitors,id_card'
        ], [], [
            'id_card' => '身份证号'
        ]);
        try {
            VisitorIssue::delete(\request('id_card'));
            return no_content();
        } catch (\Exception $exception) {
            \Log::error('删除下发异常:' . $exception->getMessage());
            return send_message('网络异常，请稍后重试', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function multiVisitor()
    {
        $this->validate(request(), [
            'id_cards' => ['required', 'array'],
            'id_cards.*' => ['required', 'exists:visitors,id_card'],
            ''
        ], [], [
            'id_cards' => '身份证号',
            'id_cards.*' => '身份证号',
        ]);
        foreach (request('id_cards') as $idCard){
            PushVisitor::dispatch($idCard);
        }
        return send_message('后台下发中...', Response::HTTP_OK);
    }


    public function multiUser()
    {
        $this->validate(request(), [
            'id_cards' => ['required', 'array'],
            'id_cards.*' => ['required', 'exists:users,id_card'],
            'access_date_from' => ['required'],
            'access_date_to' => ['required'],
            'access_time_from' => ['required'],
            'access_time_to' => ['required'],
            'limiter' => ['required'],
        ], [], [
            'id_cards' => '身份证号',
            'id_cards.*' => '身份证号',
            'access_date_from' => '起始访问日期',
            'access_date_to' => '截止访问日期',
            'access_time_from' => '起始访问时间',
            'access_time_to' => '截止访问时间',
            'limiter' => '访问次数限制'
        ]);
        foreach (request('id_cards') as $idCard){
            PushUser::dispatch(
                $idCard,
                request('access_date_from'),
                request('access_date_to'),
                request('access_time_from'),
                request('access_time_to'),
                request('limiter')
            );
        }
        return send_message('后台下发中...', Response::HTTP_OK);
    }

    public function allVisitor()
    {
        $visitors = Visitor::all();
        foreach ($visitors->pluck('id_card')->toArray() as $idCard){
            PushVisitor::dispatch($idCard);
        }
        return send_message('后台下发中...', Response::HTTP_OK);
    }

    public function allUser()
    {
        $this->validate(request(), [
            'access_date_from' => ['required'],
            'access_date_to' => ['required'],
            'access_time_from' => ['required'],
            'access_time_to' => ['required'],
            'limiter' => ['required'],
        ], [], [
            'access_date_from' => '起始访问日期',
            'access_date_to' => '截止访问日期',
            'access_time_from' => '起始访问时间',
            'access_time_to' => '截止访问时间',
            'limiter' => '访问次数限制'
        ]);
        $users = User::all();
        foreach ($users->pluck('id_card')->toArray() as $idCard){
            PushUser::dispatch(
                $idCard,
                request('access_date_from'),
                request('access_date_to'),
                request('access_time_from'),
                request('access_time_to'),
                request('limiter')
            );
        }
        return send_message('后台下发中...', Response::HTTP_OK);
    }
}
