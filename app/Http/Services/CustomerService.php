<?php

namespace App\Http\Services;

use App\Models\Customer;

/**
 * 小程序用户服务类
 * Class CustomerService
 * @package App\Http\Services
 * Author Ripper. 2022/7/11
 */
class CustomerService
{


    /**
     * 获取用户信息
     * @param $data
     * @return \App\Models\BaseModel|Customer|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function detail($user_id)
    {
        return Customer::where('id',$user_id)->first();
    }

}
