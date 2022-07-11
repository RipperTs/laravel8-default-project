<?php
/**
 * Created by PhpStorm.
 * User: xinu x-php@outlook.com
 * Coding Standard: PSR2
 * Date: 2020-03-27
 * Time: 14:38
 */
return [
    // 极光配置
    'jiguang_im_key' => env('JG_KEY', 'e3f0718571b97a51ed11fbe9'),
    'jiguang_im_secret' => env('JG_SECRET', '48ae1b2e6a5559250103ace8'),
    'jiguang_im_version' => '1',
    'jiguang_im_customer_prefix' => 'hk_',
    // 阿里云媒转码配置
    'ali_mts_pipelineid' => env("ALI_MTS_PIPELINEID", ''),
    'ali_mts_templateid' => env("ALI_MTS_TEMPLATEID", ''),
    // redis记录聊聊在线用户的key前缀
    //'im_online_key' => 'wm_onlie:',
    // 小程序jwt 授权有效期 单位 s
    'miniprogram_jwt_ttl' => env('MINIPROGRAM_TTL', 3600 * 2),
    // 默认头像地址
    'default_avatar' => 'https://huike-files.wemark.tech/mrtx.png',
    // 小程序用户授权时间过期时间 单位 小时
    'userinfo_auth_range' => 100
];
