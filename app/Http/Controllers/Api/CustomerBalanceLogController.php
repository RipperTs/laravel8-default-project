<?php

namespace App\Http\Controllers\Api;

use App\Http\Services\CustomerBalanceLogService;
use Illuminate\Http\Request;

/**
 * 余额相关控制器
 *
 * Class CustomerBalanceLogController
 * @package App\Http\Controllers\Api
 * Author Ripper. 2022/7/1
 */
class CustomerBalanceLogController extends BaseController
{

    /**
     * 余额变动服务
     *
     * @var CustomerBalanceLogService
     */
    public $service;


    public function __construct(CustomerBalanceLogService $balanceLogService)
    {
        $this->service = $balanceLogService;
    }


    /**
     * 获取变动明细列表
     * @param Request $request
     * @return string
     */
    public function lists(Request $request)
    {
        $list = $this->service->getList($request->all());
        return $this->showSuccess($list);
    }

}
