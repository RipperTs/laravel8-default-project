<?php

namespace App\Http\Controllers\Api;

use App\Http\Services\Order\WxPay;
use Illuminate\Http\Request;
use NanBei\Response\Facades\Response;

/**
 * 回调通知
 * Class NotifyController
 * @package App\Http\Controllers\Api
 * Author Ripper. 2022/6/22
 */
class NotifyController extends BaseController
{

    /**
     * 订单支付回调通知
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     */
    public function notify()
    {
        return (new WxPay())->notify();
    }

}
