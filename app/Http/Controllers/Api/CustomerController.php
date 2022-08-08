<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Services\CustomerService;

/**
 * 小程序用户相关控制器
 * Class CustomerController
 * @package App\Http\Controllers\Api
 * Author Ripper. 2022/7/11
 */
class CustomerController extends BaseController
{

    protected $service;

    public function __construct(CustomerService $service)
    {
        $this->service = $service;
    }


    /**
     * 获取用户详情信息
     * @param Request $request
     * @return string
     */
    public function detail(Request $request)
    {
        $detail = $this->service->detail($request->user_id);
        return $this->showSuccess(compact('detail'));
    }

}
