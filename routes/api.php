<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\CustomerBalanceLogController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->name('api.v1.')->group(function () {

    // 公共接口
    Route::post('order/notify', '\App\Http\Controllers\Api\OrderController@notify'); //web红包充值微信支付回调

    // 小程序登录授权类接口
    Route::prefix('auth')->group(function () {
        Route::post('user/phone', '\App\Http\Controllers\Api\WxAuthApiController@userPhoneAuth');//客户端小程序授权手机号
        Route::post('user/login', '\App\Http\Controllers\Api\WxAuthApiController@login');//客户端小程序授权
        Route::post('user/location', '\App\Http\Controllers\Api\WxAuthApiController@location');//用户授权地理位置
        Route::post('user/getAddress', '\App\Http\Controllers\Api\WxAuthApiController@getAddress');//通过经纬度获取地理位置

    });

    // 测试控制器 和设计业务无关
    Route::prefix('test')->controller(TestController::class)->group(function () {
        Route::get('test', 'test');//订单列表

    });

    //小程序登录后才可用的接口
    Route::middleware(['user.auth'])->group(function () {

        //用户管理
        Route::prefix('customer')->controller(CustomerController::class)->group(function () {
            Route::get('detail', 'detail');//用户详情信息

        });

        // 余额相关管理
        Route::prefix('balance/log')->controller(CustomerBalanceLogController::class)->group(function () {
            Route::get('lists', 'lists');//余额明细列表

        });

    });

});
