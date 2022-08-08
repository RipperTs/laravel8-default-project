<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

abstract class BaseController extends Controller
{

    /**
     * 成功返回
     * @param $data
     * @param string $msg
     * @return string
     */
    protected function showSuccess(array $data = [], string $msg = '操作成功', int $code = 0)
    {
        return $this->resJson($msg, $code, $data);
    }

    /**
     * 返回错误信息
     * @param int $code
     * @param string $msg
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function showError(int $code = 422, string $msg = '操作失败', array $data = [])
    {
        return $this->resJson($msg, $code, $data);
    }


    /**
     * json格式数据
     * @param $msg
     * @param $code
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function resJson($msg, $code, $data)
    {
        $data = array('error_code' => $code, 'message' => $msg, 'data' => $data);
        return Response::json($data);
    }


}
