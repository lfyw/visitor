<?php

namespace App\Http\Controllers\Pc;

use App\Events\OperationDone;
use App\Http\Controllers\Controller;
use App\Http\Resources\Pc\IssueResource;
use App\Jobs\PullIssue;
use App\Jobs\PushUser;
use App\Jobs\PushVisitor;
use App\Models\Issue;
use App\Models\OperationLog;
use App\Models\User;
use App\Models\Visitor;
use App\Supports\Sdks\VisitorIssue;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class IssueController extends Controller
{
    public function index()
    {
        return IssueResource::collection(Issue::filterByIdCard(sm4encrypt(\request('id_card')))->with(['gate.passageways'])->latest('id')->paginate(\request('pageSize', 10)));
    }

    public function update(Issue $issue)
    {
        try {
            //下发请求
            VisitorIssue::addByIdCard(sm4decrypt($issue->id_card), $issue->gate()->get(['ip'])->toArray());
            //成功则记录下发成功记录
            $issue->fill(['issue_status' => true])->save();
            Issue::syncIssue($issue->id_card);

            $visitor = Visitor::firstWhere('id_card', $issue->id_card);
            $visitor->fill(['actual_pass_count' => 0])->save();
            event(new OperationDone(OperationLog::VISITOR,
                sprintf(sprintf("重新下发")),
                auth()->id()));
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
            'id_card' => 'required'
        ], [], [
            'id_card' => '身份证号'
        ]);
        try {
            $visitor = Visitor::firstWhere('id_card', sm4encrypt(request('id_card')));
            PullIssue::dispatch(
                sm4decrypt($visitor->id_card),
                $visitor->name,
                $visitor->files->first()?->url,
                $visitor->access_date_from,
                $visitor->access_date_to,
                $visitor->access_time_from,
                $visitor->access_time_to,
                $visitor->limiter,
                $visitor->ways
            )->onQueue('issue');
            event(new OperationDone(OperationLog::VISITOR,
                sprintf(sprintf("删除下发")),
                auth()->id()));
            return no_content();
        } catch (\Exception $exception) {
            \Log::error('删除下发异常:' . $exception->getMessage());
            return send_message('网络异常，请稍后重试', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function multiVisitor()
    {
        $this->validate(request(), [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'exists:visitors,id'],
            'access_date_from' => ['required'],
            'access_date_to' => ['required'],
            'access_time_from' => ['required'],
            'access_time_to' => ['required'],
            'limiter' => ['required'],
        ], [], [
            'ids' => 'id',
            'ids.*' => 'id',
            'access_date_from' => '起始访问日期',
            'access_date_to' => '截止访问日期',
            'access_time_from' => '起始访问时间',
            'access_time_to' => '截止访问时间',
            'limiter' => '访问次数限制'
        ]);

        $idCards = Visitor::whereIn('id', request('ids'))->pluck('id_card')->toArray();
        foreach ($idCards as $idCard){
            PushVisitor::dispatch(sm4decrypt($idCard),
                request('access_date_from'),
                request('access_date_to'),
                request('access_time_from'),
                request('access_time_to'),
                request('limiter')
            )->onQueue('issue');
        }
        event(new OperationDone(OperationLog::VISITOR,
            sprintf(sprintf("批量下发访客")),
            auth()->id()));
        return send_message('后台下发中...', Response::HTTP_OK);
    }


    public function multiUser()
    {
        $this->validate(request(), [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'exists:users,id'],
            'access_date_from' => ['required'],
            'access_date_to' => ['required'],
            'access_time_from' => ['required'],
            'access_time_to' => ['required'],
            'limiter' => ['required'],
        ], [], [
            'ids' => 'id',
            'ids.*' => 'id',
            'access_date_from' => '起始访问日期',
            'access_date_to' => '截止访问日期',
            'access_time_from' => '起始访问时间',
            'access_time_to' => '截止访问时间',
            'limiter' => '访问次数限制'
        ]);

        $idCards = User::whereIn('id', request('ids'))->pluck('id_card')->toArray();
        foreach ($idCards as $idCard){
            PushUser::dispatch(
                sm4decrypt($idCard),
                request('access_date_from'),
                request('access_date_to'),
                request('access_time_from'),
                request('access_time_to'),
                request('limiter')
            )->onQueue('issue');
        }
        event(new OperationDone(OperationLog::VISITOR,
            sprintf(sprintf("批量下发员工")),
            auth()->id()));
        return send_message('后台下发中...', Response::HTTP_OK);
    }

    public function allVisitor()
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

        $visitors = Visitor::all();
        foreach ($visitors->pluck('id_card')->toArray() as $idCard){
            PushVisitor::dispatch(sm4decrypt($idCard),
                request('access_date_from'),
                request('access_date_to'),
                request('access_time_from'),
                request('access_time_to'),
                request('limiter')
            )->onQueue('issue');
        }
        event(new OperationDone(OperationLog::VISITOR,
            sprintf(sprintf("访客全部下发")),
            auth()->id()));
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
                sm4decrypt($idCard),
                request('access_date_from'),
                request('access_date_to'),
                request('access_time_from'),
                request('access_time_to'),
                request('limiter')
            )->onQueue('issue');
        }
        event(new OperationDone(OperationLog::VISITOR,
            sprintf(sprintf("员工全部下发")),
            auth()->id()));
        return send_message('后台下发中...', Response::HTTP_OK);
    }
}
