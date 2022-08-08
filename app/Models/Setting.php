<?php

namespace App\Models;

/**
 * 系统设置记录表
 * Class Setting
 * @package App\Models
 * Author Ripper. 2022/7/12
 */
class Setting extends BaseModel
{

    protected $table = 'settings';


    public function getValuesAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setValuesAttribute($value)
    {
        return $this->attributes['values'] = json_encode($value);
    }
}
