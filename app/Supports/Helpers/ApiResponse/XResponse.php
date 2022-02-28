<?php
namespace App\Supports\Helpers\ApiResponse;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Response;

/**
 *@method static Response noContent(int $status=204)
 *@method static Response sendData(array|string $data, int $status = 200)
 *@method static Response sendMessage(string $message, int $status = 200)
 *@method static Response error(string $message, int $status = 400, int $code = 400, array $data = [])
 *@Author:lfyw
 *@Date:2022-01-12 11:48:36
*/
class XResponse extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'xresponse';
    }
}
