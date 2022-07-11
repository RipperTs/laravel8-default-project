<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use NanBei\Response\Facades\Response;

class TestController extends BaseController
{

    public function __construct(Request $request)
    {
        //$this->projectService = new ProjectService();
    }

    /**
     * 测试签名功能
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function test(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'secret' => 'required',
            //'signature' => 'required',
        ], [
            'id.required' => 'id必传',
            'secret.required' => 'secret必传',
            //'signature.required' => 'signature必传',
        ]);

        return Response::success($request);
    }

    public function test2(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'secret' => 'required',
            //'signature' => 'required',
        ], [
            'id.required' => 'id必传',
            'secret.required' => 'secret必传',
            //'signature.required' => 'signature必传',
        ]);

        return Response::success($request);
    }

}
