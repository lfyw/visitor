<?php

namespace App\Http\Controllers\Pc;

use AlicFeng\IdentityCard\InfoHelper;
use App\Events\OperationDone;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\BlacklistRequest;
use App\Http\Resources\Pc\BlacklistResource;
use App\Models\Blacklist;
use App\Models\OperationLog;

class BlacklistController extends Controller
{
    public function index()
    {
        return BlacklistResource::collection(Blacklist::name(request('name'))->idCard(request('id_card'))->latest()->paginate(request('pageSize', 10)));
    }

    public function store(BlacklistRequest $blacklistRequest)
    {
        $validated = $blacklistRequest->validated();
        $validated['gender'] = InfoHelper::identityCard()->sex($validated['id_card']) == 'M' ? '男' : '女';
        $blacklist = Blacklist::create($validated);
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
        $blacklist->fill($validated)->save();
        event(new OperationDone(OperationLog::BLACKLIST,
            sprintf(sprintf("编辑黑名单【%s】", $blacklistRequest->name)),
            auth()->id()));
        return send_data(new BlacklistResource($blacklist));
    }

    public function destroy(Blacklist $blacklist)
    {
        $blacklist->delete();
        event(new OperationDone(OperationLog::BLACKLIST,
            sprintf(sprintf("移除黑名单【%s】", $blacklist->name)),
            auth()->id()));
        return no_content();
    }
}
