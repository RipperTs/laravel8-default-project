<?php

namespace App\Http\Middleware;

use App\Exceptions\CustomizeException;
use App\Http\Services\WxAuthService;
use App\Models\Customer;
use Closure;

/**
 * 小程序用户鉴权中间件
 * Class UserAuth
 * @package App\Http\Middleware
 * Author Ripper. 2022/7/11
 */
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
        $service = new WxAuthService();
        try {
            if (!$request->hasHeader('token')) {
                abort(401, '请先登录');
            }

            $res = $service->checkToken($request->header('token'));
            if (!Customer::query()->where('id', $res['user_id'])->exists()) {
                abort(401, '用户不存在');
            }
            $request->offsetSet('openid', $res['openid']);
            $request->offsetSet('user_id', $res['user_id']);

        } catch (CustomizeException $e) {
            abort(401, 'token失效');
        }
        return $next($request);
    }

}
