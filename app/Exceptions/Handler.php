<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Validation\ValidationException;

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
        })->stop();
    }

    /**
     * 自定义异常处理
     * @param \Illuminate\Http\Request $request
     * @param Throwable $e
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $e)
    {
        $exceptionInfo = ['file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace()];
        $message = $e->getMessage();
        if ($e instanceof HttpException) { // abort 抛出的http异常
            $error_code = $e->getStatusCode();
        } else if ($e instanceof ValidationException) { // 表单规则验证失败
            $error_code = 421;
            $message = $e->validator->getMessageBag()->toArray();
            $message = array_values($message)[0][0];
        } else {
            $error_code = 500;
            Log::error($message, $exceptionInfo);
        }


        if (config('app.debug')) {
            // 记录debug日志
            Log::debug($message, $exceptionInfo);
            return response()->json(array_merge([
                'error_code' => $error_code,
                'message' => $message ?: '系统繁忙,请稍后再试~',
                'data' => [],
            ], $exceptionInfo));
        }
        return response()->json([
            'error_code' => $error_code,
            'message' => $message ?: '系统繁忙,请稍后再试~',
            'data' => [],
        ]);
    }


}
