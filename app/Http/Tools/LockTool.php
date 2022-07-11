<?php
/**
 * Created by PhpStorm.
 * Author: xinu x-php@outlook.com
 * Coding Standard: PSR2
 * DateTime: 2021-01-22 10:22
 */


namespace App\Http\Tools;

use Illuminate\Redis\Connections\PhpRedisConnection;
use Illuminate\Support\Facades\Redis;

/**
 * Redis 加锁工具类
 * Class LockTool
 * @package App\Http\Tools
 */
class LockTool
{
    public const LOCK_PREFIX = 'hk_locl_pre:';
    // 单位 s

    /**
     * Desc: 加简易锁
     * Author: xinu
     * Time: 2020-09-15 17:26
     * @param $lockname
     * @param int $liveTime 生存周期
     * @return false|string
     */
    public static function acquireEasyLock($lockname, int $liveTime = 30)
    {
        /** @var PhpRedisConnection $redis */
        $redis = Redis::connection('cache');
        $identify = uniqid('', true);
        $end = time() + $liveTime;
        $lockname = self::LOCK_PREFIX . $lockname;
//        while (time() < $end) {
//            if ($redis->setnx($lockname, $identify)) {
//                $redis->expire($lockname, $liveTime);
//                return $identify;
//            }
//        }
        if ($redis->setnx($lockname, $identify)) {
            $redis->expire($lockname, $liveTime);
            return $identify;
        }
        return false;
    }

    /**
     * Desc: 释放锁
     * Author: xinu
     * Time: 2020-09-15 17:26
     * @param $lockname
     * @param $identify
     * @return false
     */
    public static function releaseEasyLock($lockname, $identify): bool
    {
        /** @var PhpRedisConnection $redis */
        $redis = Redis::connection('cache');
        $lockname = self::LOCK_PREFIX . $lockname;
        if ($redis->get($lockname) === $identify) {
            return $redis->del([$lockname]);
        }
        return false;
    }
}
