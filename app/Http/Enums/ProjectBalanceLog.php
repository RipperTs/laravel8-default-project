<?php


namespace App\Http\Enums;

/**
 * 项目余额变动记录枚举
 * Class ProjectBalanceLog
 * @package App\Http\Enums
 * Author Ripper. 2022/4/7
 */
class ProjectBalanceLog extends EnumBasics
{
    // 用户充值
    const RECHARGE = 10;

    // 红包发放
    const CONSUME = 20;

    // 管理员操作
    const ADMIN = 30;

    // 订单退款
    const REFUND = 40;

    /**
     * 获取订单类型值
     * @return array
     */
    public static function data()
    {
        return [
            self::RECHARGE => [
                'name' => '用户充值',
                'value' => self::RECHARGE,
                'describe' => '用户充值：%s',
            ],
            self::CONSUME => [
                'name' => '红包发放',
                'value' => self::CONSUME,
                'describe' => '红包发放：%s',
            ],
            self::ADMIN => [
                'name' => '管理员操作',
                'value' => self::ADMIN,
                'describe' => '管理员 [%s] 操作',
            ],
            self::REFUND => [
                'name' => '订单退款',
                'value' => self::REFUND,
                'describe' => '订单退款：%s',
            ],
        ];
    }

}
