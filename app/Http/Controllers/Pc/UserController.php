<?php

namespace App\Http\Controllers\Pc;

use App\Events\OperationDone;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\UserRequest;
use App\Http\Resources\Pc\UserResource;
use App\Jobs\PullIssue;
use App\Models\Auditor;
use App\Models\OperationLog;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserController extends Controller
{

    public function index()
    {
        return UserResource::collection(User::whenRealName(request('real_name'))
            ->whenRoleId(request('role_id'))
            ->whenUserStatus(request('user_status'))
            ->whenDepartmentId(request('department_id'))
            ->whenIdCard(sm4encrypt(request('id_card')))
            ->whenPhoneNumber(sm4encrypt(request('phone_number')))
            ->adminShouldBeHidden(auth()->user())
            ->canSee()
            ->with([
                'department.ancestors',
                'userType:id,name',
                'role:id,name',
                'files'
            ])
            ->paginate(request('pageSize', 10))
        );
    }

    public function store(UserRequest $userRequest)
    {
        $user = DB::transaction(function () use ($userRequest) {
            $validated = $userRequest->only(['name', 'real_name', 'department_id', 'user_type_id', 'role_id', 'user_status', 'duty', 'id_card', 'phone_number', 'issue_status']);
            $validated['password'] = bcrypt(Str::substr(Str::upper($validated['id_card']), -6, 6));
            $validated['id_card'] = sm4encrypt(Str::upper($validated['id_card']));
            $validated['phone_number'] = sm4encrypt($validated['phone_number']);
            $user = User::create($validated);
            $user->attachFiles($userRequest->face_picture_ids);
            $user->ways()->attach($userRequest->way_ids);
            return $user;
        });
        event(new OperationDone(OperationLog::USER,
            sprintf("新增人员【%s】", $userRequest->real_name),
            auth()->id()));
        return send_data(new UserResource($user->load([
            'department.ancestors',
            'userType:id,name',
            'role:id,name',
            'ways',
            'files',
            'visitor'
        ])->loadFiles()));
    }

    public function show(User $user)
    {
        return send_data(new UserResource($user->load([
            'department.ancestors',
            'userType:id,name',
            'role:id,name',
            'ways',
            'files'
        ])->loadFiles()));
    }

    public function update(UserRequest $userRequest, User $user)
    {
        $user = DB::transaction(function () use ($user, $userRequest) {
            $validated = $userRequest->only(['real_name', 'department_id', 'user_type_id', 'role_id', 'user_status', 'duty', 'id_card', 'phone_number', 'issue_status']);

            if ($user->name == User::SUPER_ADMIN) {
                unset($validated['role_id']);
            }
            $validated['id_card'] = sm4encrypt(Str::upper($validated['id_card']));
            $validated['phone_number'] = sm4encrypt($validated['phone_number']);
            $user->fill($validated)->save();
            $user->syncFiles($userRequest->face_picture_ids);
            $user->ways()->sync($userRequest->way_ids);

            Auditor::whereUserId($user->id)->first()?->fill(['user_real_name' => $userRequest->real_name])->save();
            return $user;
        });
        event(new OperationDone(OperationLog::USER,
            sprintf("编辑人员【%s】", $userRequest->real_name),
            auth()->id()));
        return send_data(new UserResource($user->load([
            'department.ancestors',
            'userType:id,name',
            'role:id,name',
            'ways'
        ])->loadFiles()));
    }

    public function destroy(UserRequest $userRequest)
    {
        User::findMany($userRequest->ids)->each(function (User $user) {
            if ($user->name !== User::SUPER_ADMIN) {
                $user->detachFiles();
                $user->ways()->detach();
                if ($visitor = Visitor::firstWhere('id_card', $user->id_card)) {
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
                }
                $user->delete();
            }
        });
        event(new OperationDone(OperationLog::USER,
            sprintf("删除人员"),
            auth()->id()));
        return no_content();
    }

    public function reset(User $user)
    {
        $this->validate(request(), [
            'password' => ['required']
        ], [], [
            'password' => '密码'
        ]);
        $user->fill(['password' => bcrypt(request('password'))])->save();
        event(new OperationDone(OperationLog::USER,
            sprintf("重置【%s】密码", $user->real_name),
            auth()->id()));
        return no_content(Response::HTTP_OK);
    }

    public function changeType()
    {
        $this->validate(request(), [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'exists:users,id'],
            'user_type_id' => ['required', 'exists:user_types,id']
        ], [], [
            'ids' => '人员id',
            'ids.*' => '人员id',
            'user_type_id' => '人员类型id'
        ]);

        $users = User::findMany(request('ids'));
        $users->each(function ($user){
            $user->fill(['user_type_id' => request('user_type_id')])->save();
        });
        return no_content();
    }
}
