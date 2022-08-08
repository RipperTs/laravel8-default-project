<?php

namespace App\Models;

use App\Http\Enums\BalanceType;

/**
 * 余额变动记录明细
 * Class CustomerBalanceLog
 * @package App\Models
 * Author Ripper. 2022/7/1
 */
class CustomerBalanceLog extends BaseModel
{

    protected $table = 'customer_balance_logs';

    protected $appends = [
        'scene_text',
        'years',
        'describe_html',
    ];


    /**
     * 关联小程序用户
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'user_id');
    }


    /**
     * 格式化场景值
     * @return mixed|string
     */
    public function getSceneTextAttribute()
    {
        return BalanceType::data()[$this->scene]['name'] ?? '其他';
    }

    public function getYearsAttribute()
    {
        return date('Y', strtotime($this->created_at));
    }


    public function getDescribeHtmlAttribute()
    {
        $content = preg_replace_callback(
            [
                '/“(.*?)”/',
            ],
            function ($matches) {
                return '“<span style="color: #969799">' . subtext($matches[1],15) . '</span>”';
            }, $this->describe);
        return $content;
    }

}
