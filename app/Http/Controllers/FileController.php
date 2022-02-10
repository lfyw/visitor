<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileRequest;
use Lfyw\FileManager\Models\File;
use App\Http\Resources\FileResource;

class FileController extends Controller
{
    public function store(FileRequest $fileRequest)
    {
        return send_data(new FileResource(File::upload($fileRequest->file('file'), request('keep_origin_name', false), request('guess_extension', true))));
    }
}
