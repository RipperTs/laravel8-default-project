<?php

namespace App\Http\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{

    /**
     * 获取指定项设置
     * @param $key
     * @return array
     */
    public static function getItem($key)
    {
        $data = self::getAll();
        return isset($data[$key]) ? $data[$key]['values'] : [];
    }

    /**
     * 获取设置项信息
     * @param $key
     */
    public static function detail($key)
    {
        return Setting::where(compact('key'))->first();
    }

    /**
     * 全局缓存: 系统设置
     * @return array|mixed
     */
    public static function getAll()
    {
        $wxapp_id = 10001;
        if (!$data = Cache::get('setting_' . $wxapp_id)) {
            $setting = Setting::all();
            $data = empty($setting) ? [] : array_column($setting->toArray(), null, 'key');
            Cache::put('setting_' . $wxapp_id, $data);
        }
        return $data;
    }

}
