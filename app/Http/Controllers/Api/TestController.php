<?php

namespace App\Http\Controllers\Api;

use App\Http\Services\SettingService;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use NanBei\Response\Facades\Response;

class TestController extends BaseController
{

    /**
     * 测试签名功能
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function test(Request $request)
    {
        var_dump(Cache::get('setting_' . 10001));
        $res = SettingService::getItem('system');
        var_dump($res);die;
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
