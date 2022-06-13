<?php

namespace App\Http\Controllers\Pc;

use AlicFeng\IdentityCard\InfoHelper;
use App\Events\OperationDone;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\BlacklistRequest;
use App\Http\Resources\Pc\BlacklistResource;
use App\Jobs\PullIssue;
use App\Models\Blacklist;
use App\Models\OperationLog;
use App\Models\Visitor;
use Illuminate\Support\Str;

class BlacklistController extends Controller
{
    public function index()
    {
        return BlacklistResource::collection(Blacklist::name(request('name'))->idCard(sm4encrypt(request('id_card')))->latest()->paginate(request('pageSize', 10)));
    }

    public function store(BlacklistRequest $blacklistRequest)
    {
        $validated = $blacklistRequest->validated();
        $validated['gender'] = InfoHelper::identityCard()->sex($validated['id_card']) == 'M' ? '男' : '女';
        $validated['id_card'] = sm4encrypt(Str::upper($validated['id_card']));
        $validated['phone'] = sm4encrypt($validated['phone']);
        $blacklist = Blacklist::create($validated);

        $visitor = Visitor::firstWhere('id_card', $blacklist->id_card)->loadFiles();
        $visitor->blockBlacklist();
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
        event(new OperationDone(OperationLog::BLACKLIST,
            sprintf(sprintf("将【%s】加入黑名单", $blacklistRequest->name)),
            auth()->id()));
        return send_data(new BlacklistResource($blacklist));
    }

    public function show(Blacklist $blacklist)
    {
        return send_data(new BlacklistResource($blacklist));
    }

    public function update(BlacklistRequest $blacklistRequest, Blacklist $blacklist)
    {
        $validated = $blacklistRequest->validated();
        $validated['gender'] = InfoHelper::identityCard()->sex($validated['id_card']) == 'M' ? '男' : '女';
        $validated['id_card'] = sm4encrypt(Str::upper($validated['id_card']));
        $validated['phone'] = sm4encrypt($validated['phone']);
        $blacklist->fill($validated)->save();
        event(new OperationDone(OperationLog::BLACKLIST,
            sprintf(sprintf("编辑黑名单【%s】", $blacklistRequest->name)),
            auth()->id()));
        return send_data(new BlacklistResource($blacklist));
    }

    public function destroy(Blacklist $blacklist)
    {
        Visitor::firstWhere('id_card', $blacklist->id_card)->cancelBlacklist();
        $blacklist->delete();
        event(new OperationDone(OperationLog::BLACKLIST,
            sprintf(sprintf("移除黑名单【%s】", $blacklist->name)),
            auth()->id()));
        return no_content();
    }

    public function block()
    {
        $this->validate(request(), [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'exists:visitors,id'],
            'blanklist_reason' => ['required']
        ], [], [
            'ids' => '拉黑人员',
            'ids.*' => '拉黑人员',
            'blanklist_reason' => '拉黑理由'
        ]);
        Visitor::findMany(request('ids'))->each(function (Visitor $visitor){
            Blacklist::updateOrCreate([
                'id_card' => $visitor->id_card
            ],[
                'name' => $visitor->name,
                'gender' => $visitor->gender,
                'phone' => $visitor->phone,
                'reason' => request('blanklist_reason'),
            ]);
            $visitor->blockBlacklist();
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
        });
        return no_content();
    }

    public function cancel()
    {
        $this->validate(request(), [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'exists:blacklists,id'],
        ], [], [
            'ids' => '拉黑人员',
            'ids.*' => '拉黑人员',
        ]);
        Blacklist::findMany(request('ids'))->each(function (Blacklist $blacklist){
            Visitor::firstWhere('id_card', $blacklist->id_card)->cancelBlacklist();
        });
        Blacklist::destroy(request('ids'));
        return no_content();
    }
}
