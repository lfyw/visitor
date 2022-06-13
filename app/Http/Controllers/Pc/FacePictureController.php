<?php

namespace App\Http\Controllers\Pc;

use App\Events\OperationDone;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\FacePictureRequest;
use App\Models\OperationLog;
use App\Models\User;
use App\Models\Visitor;

class FacePictureController extends Controller
{
    public function store(FacePictureRequest $request)
    {
        foreach ($request->face_pictures as $facePicture) {
            if ($request->type == 'user') {
                if ($user = User::firstWhere('id_card', sm4encrypt($facePicture['id_card']))) {
                    $user->syncFiles($facePicture['id']);
                }
            } else {
                if ($visitor = Visitor::firstWhere('id_card', sm4encrypt($facePicture['id_card']))) {
                    $visitor->syncFiles($facePicture['id']);
                }
            }
        }

        event(new OperationDone(request('type') == 'user' ? OperationLog::USER : OperationLog::VISITOR,
            sprintf(sprintf("更新%s面容照片", request('type') == 'user' ? '员工' : '访客')),
            auth()->id()));

        return no_content();
    }
}
