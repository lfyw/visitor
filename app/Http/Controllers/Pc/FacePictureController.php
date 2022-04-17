<?php

namespace App\Http\Controllers\Pc;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pc\FacePictureRequest;
use App\Models\User;
use App\Models\Visitor;

class FacePictureController extends Controller
{
    public function store(FacePictureRequest $request)
    {
        foreach ($request->face_pictures as $facePicture) {
            if ($request->type == 'user') {
                if ($user = User::firstWhere('id_card', $facePicture['id_card'])) {
                    $user->syncFiles($facePicture['id']);
                }
            } else {
                if ($visitor = Visitor::firstWhere('id_card', $facePicture['id_card'])) {
                    $visitor->syncFiles($facePicture['id']);
                }
            }
        }
        return no_content();
    }
}
