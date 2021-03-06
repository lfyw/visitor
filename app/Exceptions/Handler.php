<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function(ValidationException $e){
            return error(
                current($e->errors())[0],
                status:Response::HTTP_UNPROCESSABLE_ENTITY,
                data:$e->errors()
            );
        });
        $this->renderable(fn(NotFoundHttpException $e) => error(
            '数据不存在或请求路径错误',
            status:Response::HTTP_NOT_FOUND
            )
        );
        $this->renderable(fn(UnauthorizedException $e) => error(
            '未登录',
            status:Response::HTTP_UNAUTHORIZED
        )
        );

        $this->renderable(fn(AccessDeniedHttpException $e) => error(
            '权限不足',
            status:Response::HTTP_FORBIDDEN
        ));

        $this->renderable(fn(MissingVisitorSettingException $e) => error(
            $e->getMessage(),
            status:$e->getCode() ?? Response::HTTP_NOT_FOUND
        ));

        if (config('app.env') == 'production'){
            $this->renderable(fn(Exception $e) => error(
                $e->getMessage(),
                status:($e->getCode() && is_int($e->getCode())) ?: Response::HTTP_INTERNAL_SERVER_ERROR,
                data:[
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                    'trace' => $e->getTrace()
                ]
            )
            );
        }
    }
}
