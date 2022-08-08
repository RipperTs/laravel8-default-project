<?php

namespace App\Models;

use App\Http\Enums\WithdrawalApplyStatus;

/**
 * 用户提现明细
 * Class CustomerWithdraw
 * @package App\Models
 * Author Ripper. 2022/7/1
 */
class CustomerWithdraw extends BaseModel
{

    protected $table = 'customer_withdraws';

    protected $appends = [
        'apply_status_text',
    ];


    /**
     * 关联小程序用户
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'user_id');
    }


    public function getApplyStatusTextAttribute()
    {
        return WithdrawalApplyStatus::data()[$this->apply_status]['name'] ?? '其他';
    }

}
