<?php

namespace App\Http\Middleware;

use App\Exceptions\CustomizeException;
use App\Http\Services\ProjectService;
use App\Http\Services\WxAuthService;
use Closure;

class UserAuth
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        try {
            if (!$request->hasHeader('x-token')) {
                throw new CustomizeException('请携带token访问', 6001);
            }

            $service = new ProjectService();

            $res = $service->checkToken($request->header('x-token'));

            if (!isset($res['appid'])) {
                return response()->json([
                    'code' => 6001,
                    'msg' => '用户token错误',
                ]);
            }
//            if ($res['appid'] != $request->appid)
//            {
//                return response()->json([
//                    'code' => 402,
//                    'msg' => '账户信息与appid不一致',
//                ]);
//            }

            $userinfo=Project::query()->where('appid', $res['appid'])->first();

            if (!$userinfo) {
                return response()->json([
                    'code' => 6001,
                    'msg' => '用户不存在',
                ]);
            }
            //判断token是否过期
            if ($userinfo->tokend_at < date('Y-m-d H:i:d'))
            {
                return response()->json([
                    'code' => 6002,
                    'msg' => 'token已过期，请重新获取',
                ]);
            }
            $appid = $res['appid'] ?? $userinfo->appid;

            //验证签名是否正确
            $signature = $request->signature;
            $sq = $this->generate($request->toArray(),$userinfo->appsecret);
            if ($signature != $sq)
            {
                return response()->json([
                    'code' => 6003,
                    'msg' => '签名错误，非法访问',
                ]);
            }
            $request->offsetSet('appid', $appid);

        } catch (CustomizeException $e) {
            return response()->json([
                'code' => 6001,
                'msg' => 'token失效',
            ]);
        }
        return $next($request);
    }

    /**
     * 生成签名
     * @param array  $params    请求参数
     * @param string $appSecret 密钥
     * @return string
     */
    public function generate(array $params, string $appSecret): string
    {

        // 1.删除参数组中所有等值为FALSE的参数（包括：NULL, 空字符串，0， false）
        $params = array_filter($params);

        // 2.按照键名对参数数组进行升序排序
        ksort($params);

        // 3.给参数数组追加app_secret的值
        $params['app_secret'] = $appSecret;

        // 4.生成 URL-encode 之后的请求字符串
        $str = http_build_query($params);

        // 5.将请求字符串使用MD5加密后，再转换成大写
        return strtoupper(MD5($str));
    }
}
