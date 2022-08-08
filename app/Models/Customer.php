<?php

namespace App\Models;

/**
 * 小程序用户表
 * Class Customer
 * @package App\Models
 * Author Ripper. 2022/7/11
 */
class Customer extends BaseModel
{

    protected $table = 'customers';

    protected $hidden = [
        'ip'
    ];

}
