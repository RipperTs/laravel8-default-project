<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\CustomizeException;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\ValidationException;
use NanBei\Response\Facades\Response;
use App\Http\Services\WxAuthService;

/**
 * 微信小程序授权api
 * Class WxAuthApiController
 * @package App\Http\Controllers\Api
 * Author Ripper. 2022/7/11
 */
class WxAuthApiController extends BaseController
{


    /**
     * 小程序用户登录
     * @param Request $request
     * @param WxAuthService $service
     * @return string
     */
    public function login(Request $request, WxAuthService $service)
    {
        $request->validate([
            'code' => 'required|string',
            'user_info' => 'required',
        ], [
            'code.required' => 'code必填',
        ]);
        $inputData = $request->only(['code', 'user_info']);
        $res = $service->auth($inputData);
        return $this->showSuccess($res);
    }


    /**
     * 小程序授权手机号
     * @param Request $request
     * @param WxAuthService $service
     * @return \Illuminate\Http\JsonResponse|string
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function userPhoneAuth(Request $request, WxAuthService $service)
    {
        $request->validate([
            'encryptedData' => 'required|string',
            'iv' => 'required|string',
        ], [
            'encryptedData.required' => 'encryptedData必填',
            'iv.required' => 'iv必填',
        ]);

        $service->phone($request->all);
        return $this->showSuccess();
    }

    /**
     * Notes:用户授权地理位置，记录位置
     * User: zyj
     * DateTime: 2021-09-15 13:11
     * @param Request $request
     * Uri:api/v1/auth/user/getAddress
     */
    public function location(Request $request)
    {
        $request->validate([
            'cid' => 'required|integer',
            'longitude' => 'required',
            'latitude' => 'required',
        ]);
        $data = array(
            'cid' => $request->cid,
            'activity_id' => $request->activity ?? 0,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'nation' => $request->nation,
            'province' => $request->province,
            'city' => $request->city,
            'district' => $request->district,//区
            'street' => $request->street,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        $ret = CustomerPosition::query()->insert($data);
        return response()->json($ret, 201);
    }

    /**
     * Notes:通过经纬度获取具体位置
     * User: zyj
     * DateTime: 2022-03-24 20:23
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\never
     * Uri:api/v1/auth/user/getAddress
     */
    public function getAddress(Request $request)
    {

        $request->validate([
            'lng' => 'required',
            'lat' => 'required',
        ]);
        $lat = $request->lat;//纬度
        $lng = $request->lng;//经度

        $pro_id = $request->pro_id;
        $cid = $request->cid;
        $thumb_address = isset($request->thumb_address) ? $request->thumb_address : '';//缩略的详细地址

        $key = 'clockin:' . $pro_id . '_' . $cid;
        $repeatedly = Redis::get($key);
        if ($repeatedly) {
            return abort(422, '请稍后再试');
        } else {
            Redis::set($key, $lat . ',' . $lng);
            Redis::expire($key, 1);//10秒
        }
        $url = "https://apis.map.qq.com/ws/geocoder/v1/?location=" . $lat . "," . $lng . "&key=ZJFBZ-7MT64-VI7U4-DIGT5-WJTRV-YNBCW&get_poi=0";

        $res = curl_get($url);

        $res = json_decode($res);

        if (isset($res->status) && $res->status !== 0) {
            return abort(422, '未获取地址信息');
        }

        $address = $res->result->address ?? '';
        $city_code = $res->result->ad_info->adcode ?? '';
        $nation = $res->result->address_component->nation ?? '';
        $province = $res->result->address_component->province ?? 'pp' . $request->source;
        $city = $res->result->address_component->city ?? 'sss' . $request->source;
        $district = $res->result->address_component->district ?? '';
        $street = $res->result->address_component->street ?? '';

        $data = array(
            'cid' => $request->cid,
            'activity_id' => $request->activity_id ?? 0,
            'longitude' => $lng,
            'latitude' => $lat,
            'nation' => $nation,
            'province' => $province,
            'city' => $city,
            'city_code' => $city_code,
            'district' => $district,//区
            'street' => $street,
            'address' => $address,
            'thumb_address' => $thumb_address,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        $ret = CustomerPosition::query()->insert($data);

        if ($request->source == '1') {//如果是个人信息的位置授权需要修改个人信息
            //更新用户定位信息
            Customer::query()->where('id', $request->cid)->update(['lat' => $lat, 'lng' => $lng, 'city_code' => $city_code, 'address' => $address, 'thumb_address' => $thumb_address, 'country' => $nation, 'province' => $province, 'city' => $city, 'district' => $district, 'street' => $street]);

            //如果有活动id，证明是在活动中授权地理位置，需要判断是否在范围内
            if ($request->activity_id) {
                $activity_info = Activity::query()->where('id', $request->activity_id)->first(['id', 'pro_id', 'cid', 'is_auth', 'is_phone', 'is_location', 'lng', 'lat', 'location_cate', 'city_code', 'province', 'city', 'district', 'address', 'thumb_address']);
                if (!empty($activity_info)) {

                    $userinfo = Customer::query()->where('id', $cid)->first(['id', 'lng', 'lat', 'city_code', 'province', 'city', 'district', 'address']);
                    if (empty($userinfo)) {
                        abort(422, '用户信息错误');
                    }
                    //需要判断地理位置是否符合
                    $customerService = new CustomerService($request->activity_id);
                    $customerService->checkAuth($userinfo, $activity_info);
                }
            }

        }
        $return_data = ['lat' => $lat, 'lng' => $lng, 'city_code' => $city_code, 'thumb_address' => $thumb_address, 'address' => $address, 'nation' => $nation, 'province' => $province, 'city' => $city, 'district' => $district, 'street' => $street];
        return Response::success($return_data);
    }

    private function ajaxReturn($msg = '', $code = 200, $data = [])
    {
        return response()->json(['data' => $data, 'msg' => $msg, 'code' => $code]);
    }


    /**
     * 客服消息推送配置url
     * @param Request $request
     * @throws CustomizeException
     */
    public function beingPushedToUrl(Request $request)
    {
        $pro_id = $request->pro_id;
        return (new WxCustomerMsgService($pro_id))->beingPushedToUrl();
    }
}
