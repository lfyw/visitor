<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Http\Resources\Pc\IssueResource;
use App\Models\Issue;
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


}
