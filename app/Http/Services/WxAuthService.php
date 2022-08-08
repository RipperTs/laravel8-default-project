<?php

namespace App\Http\Services;

use App\Exceptions\CustomizeException;
use App\Http\Tools\JwtTool;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use EasyWeChat\Factory;
use Symfony\Component\Cache\Adapter\RedisAdapter;

/**
 * 小程序授权相关服务
 * Class WxAuthService
 * @package App\Http\Services
 * Author Ripper. 2022/7/11
 */
class WxAuthService
{
    const KEY = 'dou,duorexue';

    /**
     * 配置详情
     * @var array
     */
    public $config;

    /**
     * 服务实例
     * @var \EasyWeChat\MiniProgram\Application
     */
    public $app;

    /**
     * 初始化信息
     * WxCustomerMsgService constructor.
     */
    public function __construct()
    {
        // 获取系统设置
        $setting = SettingService::getItem('system');
        // 配置详情
        $this->config = [
            'app_id' => $setting['wechat_app_id'],
            'secret' => $setting['wechat_app_secret'],
            'response_type' => 'array',
        ];
        $this->app = Factory::miniProgram($this->config);
        // 使用redis作为SDK缓存
        $predis = app('redis')->connection()->client();
        $cache = new RedisAdapter($predis);
        $this->app->rebind('cache', $cache);
    }

    /**
     * 小程序授权
     * @param $inputData
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function auth($inputData = array()): array
    {
        $code = $inputData['code'];
        $referee_id = $inputData['referee_id'] ?? 0;//上级用户ID
        $authInfo = $this->app->auth->session($code);
        if (array_key_exists('errcode', $authInfo)) {
            abort(422, $authInfo['errcode'] . $authInfo['errmsg']);
        }
        $user_info = json_decode($inputData['user_info'], true);
        if (!$user_info) {
            abort(422, '登录失败');
        }

        $customerInfo = Customer::query()->where('open_id', $authInfo['openid'])->first();

        $saveData = [
            'nickname' => $user_info['nickName'],
            'gender' => $user_info['gender'],
            'open_id' => $authInfo['openid'],
            'session_key' => $authInfo['session_key'],
            'ip' => getClientRealIP(),
            'head_img' => $user_info['avatarUrl'],
        ];
        if (!$customerInfo) {
            $customerInfo = Customer::query()->create($saveData);
        } else {
            $customerInfo->update($saveData);
        }

        // 颁发token
        $token = $this->jwtEncode([
            'user_id' => $customerInfo->id,
            'openid' => $customerInfo->open_id,
        ]);
        return [
            'token' => $token,
            'user_id' => $customerInfo->id,
        ];
    }


    /**
     * Notes:授权电话
     * User: zyj
     * DateTime: 2022-03-26 18:05
     * @param array $inputData
     * @return mixed
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * Uri:
     */
    public function phone($inputData = array())
    {
        $code = $inputData['code'] ?? '';
        $cid = $inputData['cid'];
        $encryptedData = $inputData['encryptedData'] ?? '';
        $is_sessionkey = $inputData['is_sessionkey'] ?? '';
        $iv = urldecode($inputData['iv']) ?? '';

        //判断session_key是否存在
        $user_info = DB::table('customers')->where('id', $cid)->first(['id', 'session_key', 'openid', 'unionid', 'wxphone']);
        if ($is_sessionkey == 1 && $user_info->session_key) {
            $session_key = $user_info->session_key;
        } else {
            $authInfo = $this->app->auth->session($code);

            if (array_key_exists('errcode', $authInfo)) {
                abort(500, '授权失败' . $authInfo['errcode']);
            }
            $session_key = $authInfo['session_key'];
        }

        $decryptedData = $this->app->encryptor->decryptData($session_key, $iv, $encryptedData);
        $phone = $decryptedData['phoneNumber'];

        //查询用户是否已经授权电话，如果已经授权，不需要在授权
        $info = Customer::query()->where('id', $cid)->first(['wxphone']);
        if (!empty($info) && !$info->wxphone) {

            //更新用户电话
            if ($phone) {
                $res = Customer::query()->where('id', $cid)->update(['wxphone' => $phone]);
            }
            return $phone;
        }

    }

    /**
     * User: xinu
     * Time: 2020-06-16 10:10
     * Desc: jwt 加密函数
     * Uri :
     * @param array $para
     * @return string
     * @throws \Exception
     */
    public function jwtEncode($para = [])
    {
        $now = time();
        $exp = $now + getenv('MINIPROGRAM_TTL');
        $para = array_merge(['exp' => $exp], $para);
        return JwtTool::encode($para, $this->getKey());
    }

    /**
     * User: xinu
     * Time: 2020-06-16 10:11
     * Desc: jwt解密函数
     * Uri :
     * @param string $token
     * @return array
     * @throws \Exception
     */
    public function jwtDecode($token = '')
    {
        try {
            return JwtTool::decode($token, $this->getKey());
        } catch (\Exception $e) {
            throw new CustomizeException('登录已过期', 401111);
        }
    }


    public function checkToken($token)
    {
        return $this->jwtDecode($token);
    }

    /**
     * User: xinu
     * Time: 2020-06-16 10:11
     * Desc: 获取加密key
     * Uri :
     * @return string
     */
    private function getKey()
    {
        return md5(self::KEY . self::KEY);
    }

    public static function generateNum()
    {
        return strtoupper(md5(uniqid(mt_rand(), true)));
    }


}
