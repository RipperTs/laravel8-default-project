<?php

namespace App\Http\Middleware;

use Closure;

class SysAdminLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // $log = SysAdminLogService::initRequestData();
        $response = $next($request);
        // if ($log) {
        //     SysAdminLogService::update($log, ['response_content' => $response->getContent(), 'response_stats_code' => $response->getStatusCode()]);
        // }
        return $response;
    }
}
