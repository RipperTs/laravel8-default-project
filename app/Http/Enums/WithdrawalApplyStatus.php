<?php

namespace App\Http\Enums;

/**
 * 提现申请类型枚举
 * Class WithdrawalApplyStatus
 * @package App\Http\Enums
 * Author Ripper. 2022/7/1
 */
class WithdrawalApplyStatus extends EnumBasics
{

    // 待审核
    const PENDING_REVIEW = 10;
    // 审核通过
    const APPROVED = 20;
    // 驳回
    const REJECTED = 30;
    // 已打款
    const PAID = 40;


    /**
     * 获取奖品类型值
     * @return array
     */
    public static function data()
    {
        return [
            self::PENDING_REVIEW => [
                'name' => '待审核',
                'value' => self::PENDING_REVIEW,
            ],
            self::APPROVED => [
                'name' => '审核通过',
                'value' => self::APPROVED,
            ],
            self::REJECTED => [
                'name' => '驳回',
                'value' => self::REJECTED,
            ],
            self::PAID => [
                'name' => '已打款',
                'value' => self::PAID,
            ],
        ];
    }

    /**
     * 获取类型名称
     * @return array
     */
    public static function getTypeName()
    {
        static $names = [];
        if (empty($names)) {
            foreach (self::data() as $item)
                $names[$item['value']] = $item['name'];
        }
        return $names;
    }

}
