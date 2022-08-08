<?php

namespace App\Http\Enums;

/**
 * 余额枚举
 * Class BalanceType
 * @package App\Http\Enums
 * Author Ripper. 2022/7/1
 */
class BalanceType extends EnumBasics
{
    // 提现
    const WITHDRAWAL = 10;
    // 提现服务费
    const WITHDRAWAL_SERVICE_FEE = 11;
    // 提现驳回返还
    const WITHDRAWAL_REJECT_RETURN = 12;
    // 充值
    const RECHARGEABLE = 20;
    // 抵扣
    const DEDUCTION = 30;

    /**
     * 获取奖品类型值
     * @return array
     */
    public static function data()
    {
        return [
            self::WITHDRAWAL => [
                'name' => '用户提现',
                'value' => self::WITHDRAWAL,
                'describe' => '余额提现',
            ],
            self::WITHDRAWAL_SERVICE_FEE => [
                'name' => '提现服务费',
                'value' => self::WITHDRAWAL_SERVICE_FEE,
                'describe' => '提现服务费',
            ],
            self::WITHDRAWAL_REJECT_RETURN => [
                'name' => '提现驳回',
                'value' => self::WITHDRAWAL_REJECT_RETURN,
                'describe' => '提现驳回返还',
            ],
            self::RECHARGEABLE => [
                'name' => '余额充值',
                'value' => self::RECHARGEABLE,
                'describe' => '余额充值',
            ],
            self::DEDUCTION => [
                'name' => '余额抵扣',
                'value' => self::DEDUCTION,
                'describe' => '消费“%s”抵扣',
            ],

        ];
    }

}
