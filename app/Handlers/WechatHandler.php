<?php

namespace App\Handlers;

use App\Models\ProjectGroupInfo;
use Faker\Provider\Image;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Project;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;

//use wechat\Check;
use App\Handlers\wechat\Check;

class WechatHandler
{
    /**
     * Created by PhpStorm.
     * ps:获取小程序二维码短码
     */
    public function getQrcodeLimit($urlc = 'wxa/getwxacodeunlimit', $path = 'pages/welcome/welcome', $scene, $proid, $is_hyaline = TRUE,$type = 2)
    {
        $pro_id = empty($proid) ? $_SESSION['pro_id'] : $proid;

        $token = $this->getToken($pro_id, $type);

        if (!$token) {
            return false;
        }

        $url = getenv('WXURL') . $urlc . '?access_token=' . $token;
        $postdata['page'] = $path;
        $postdata['scene'] = $scene;
        $postdata['width'] = 1280;
        $postdata['is_hyaline'] = $is_hyaline;

        $imgflow = $this->https_request($url, $postdata, 'json');

        $filepath = $this->saveimg($imgflow,'home/qrcode');
        return $filepath;
    }

    /**
     * Created by PhpStorm.
     * ps:http请求
     */
    function https_request($url, $data = '', $type = '', $times = 1)
    {
        if ($type == 'json') {//json $_POST=json_decode(file_get_contents('php://input'), TRUE);
            $headers = array("Content-type: application/json;charset=UTF-8", "Accept: application/json", "Cache-Control: no-cache", "Pragma: no-cache");
            $data = json_encode($data);
        } else {
            $headers = array('User-Agent:Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36');
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($curl);
        // 显示错误信息
        if (curl_error($curl)) {
//            print "Error: " . curl_error($curl);
            return false;
        } else {
            // 打印返回的内容
            curl_close($curl);
        }

        return $output;
    }


    /**
     * Created by PhpStorm.
     * ps:获取公众号token
     */
    function getGzhToken($pro_id = '', $cache = 0)
    {
        $pro_id = $pro_id ?? $_SESSION['pro_id'];
        $pro_info = ProjectGroupInfo::where('project_id', $pro_id)->first();

        if (empty($pro_info)) {
            return false;
        }

        $pro_info = $pro_info->toArray();
        $appid = $pro_info['wechat_appid'];
        $secret = $pro_info['wechat_secret'];
        $accesskey = 'wechat_access_token' . $appid;
        $token = $this->getCache($accesskey);

        if (empty($token) || $cache == 1) {
            $url = getenv('WXURL') . 'cgi-bin/token?grant_type=' . getenv('GRANT_TYPE') . '&appid=' . $appid . '&secret=' . $secret;
            $token = $this->https_request($url);
            $json = json_decode($token);
            if ($json->access_token) {
                $this->setCache('wechat_access_token' . $appid, $json->access_token, 7000);
                $token = $json->access_token;
            } else {
                return false;
            }
        }

        return $token;
    }

    /**
     * Created by PhpStorm.
     * ps:获取企业微信token
     */
    function getQyToken($pro_id = '', $cache = 0, $type = '')
    {
        $pro_id = $pro_id ?? $_SESSION['pro_id'];
        $pro_info = DB::table('project_group_info')->where('project_id', $pro_id)->first();

        if (empty($pro_info)) {
            return false;
        }

        $config['corpid'] = $pro_info->corpid;

        if ($type == 'department') {
            //通讯录秘钥
            $accesskey = 'wechat_access_token_qy_department_' . $config['corpid'] . $pro_id;
            $config['corpsecret'] = $pro_info->department_secret;
        } elseif ($type == 'app') {
            //应用秘钥
            $accesskey = 'wechat_access_token_qy_agentid_' . $config['corpid'] . $pro_id;
            $config['corpsecret'] = $pro_info->agentid_secret;
        } elseif ($type == 'contact_way') {
            //客户联系秘钥
            $accesskey = 'wechat_access_token_qy_contact_way_' . $config['corpid'] . $pro_id;
            $config['corpsecret'] = $pro_info->contact_way_secret;
        } elseif ($type == 'xyx') {
            //写一写
            $accesskey = 'wechat_access_token_qy_xyx_' . $config['corpid'] . $pro_id;
            $config['corpsecret'] = $pro_info->xyx_secret;
        } elseif ($type == 'vr') {
            //vr
            $accesskey = 'wechat_access_token_qy_vr_' . $config['corpid'] . $pro_id;
            $config['corpsecret'] = $pro_info->vr_secret;
        } else {
            //企业秘钥
            $accesskey = 'wechat_access_token_qy_' . $config['corpid'] . $pro_id;
            $config['corpsecret'] = $pro_info->corpsecret;
        }

        $token = $this->getCache($accesskey);


        if (empty($token) || $cache == 1) {

            $body = $config;
            $apiUrl = 'https://qyapi.weixin.qq.com/';
            $apiStr = 'cgi-bin/gettoken';

            $res = http_post($body, $apiStr, $apiUrl);
            $data = json_decode($res);

            if ($data->access_token) {
                $this->setCache($accesskey, $data->access_token, 7200);
                $token = $data->access_token;
            } else {
                return false;
            }
        }

        return $token;
    }


    /**
     * Created by PhpStorm.
     * ps:获取企业的jsapi_ticket
     */
    function getJsapiTicket($access_token, $pro_id)
    {
        $pro_id = $pro_id ?? $_SESSION['pro_id'];

        if (!$access_token || !$pro_id) {
            return false;
        }

        $accesskey = 'wechat_qywx_apijs_ticket_' . $pro_id;

        $ticket = $this->getCache($accesskey);

        if (empty($ticket)) {

            $body = [];
            $apiStr = '/cgi-bin/get_jsapi_ticket?access_token=' . $access_token;
            $apiUrl = 'https://qyapi.weixin.qq.com/';

            $res = http_post($body, $apiStr, $apiUrl);
            $data = json_decode($res);

            if ($data->ticket) {
                $this->setCache($accesskey, $data->ticket, 7200);
                $ticket = $data->ticket;
            } else {
                return false;
            }
        }

        return $ticket;
    }


    /**
     * Created by PhpStorm.
     * ps:获取企业微信token
     */
    function getQyFwsToken($pro_id = '', $cache = 0)
    {
        $pro_id = $pro_id ?? $_SESSION['pro_id'];
        $pro_info = ProjectGroupInfo::where('project_id', $pro_id)->first();

        if (empty($pro_info)) {
            return false;
        }

        $config['corpid'] = $pro_info->fws_corpid;
        $config['provider_secret'] = $pro_info->fws_secret;

        $accesskey = 'wechat_access_token_provider_' . $config['corpid'];
        $token = $this->getCache($accesskey);

        if (empty($token) || $cache == 1) {

            $body = $config;
            $apiUrl = 'https://qyapi.weixin.qq.com/';
            $apiStr = 'cgi-bin/service/get_provider_token';

            $res = http_post($body, $apiStr, $apiUrl);
            $data = json_decode($res);

            if ($data->provider_access_token) {
                $this->setCache($accesskey, $data->provider_access_token, 7200);
                $token = $data->provider_access_token;
            } else {
                return false;
            }
        }

        return $token;
    }


    /**
     * Created by PhpStorm.
     * ps:获取token
     * $type 1是管理端小程序 2是客户端小程序
     */
    function getToken($pro_id = '', $type = 1,$cache = 0)
    {
        $pro_id = $pro_id ?? $_SESSION['pro_id'];
        $pro_info = Project::where('id', $pro_id)->first();

        if (empty($pro_info)) {
            return false;
        }

        $pro_info = $pro_info->toArray();
        if ($type==1)
        {
            $appid = $pro_info['manage_appid'];
            $secret = $pro_info['manage_appsecret'];

        }else{
            $appid = $pro_info['appid'];
            $secret = $pro_info['appsecret'];
        }
        $accesskey = 'code_access_token' . $appid;
        $token = $this->getCache($accesskey);

        if (empty($token) || $cache == 1) {
            $url = getenv('WXURL') . 'cgi-bin/token?grant_type=' . getenv('GRANT_TYPE') . '&appid=' . $appid . '&secret=' . $secret;
            $token = $this->https_request($url);

            $json = json_decode($token);
            if ($json->access_token) {
                $this->setCache('code_access_token' . $appid, $json->access_token, 7000);
                $token = $json->access_token;
            } else {
                return false;
            }
        }

        return $token;
    }

    public function sendWxTempMessage($pro_id, $touseropenid, $tmp_id, $data, $page = '')
    {
        $url = getenv('WXURL') . 'cgi-bin/message/subscribe/send?access_token=';
        if (empty($pro_id) || empty($touseropenid) || empty($tmp_id) || empty($data)) {
            return false;
        }
        $token = $this->getToken($pro_id);
        if (!$token) return false;
        $url .= $token;
        $postdata = [];
        $postdata['touser'] = $touseropenid;
        $postdata['template_id'] = $tmp_id;
        $postdata['page'] = $page;
        $postdata['data'] = $data;
        $json_res = $this->https_request($url, $postdata, 'json');
        if ($res = json_decode($json_res)) {
            if ($res->errcode === 0) {
                return true;
            }
            Log::error("模板留言失败：{$res->errcode} {$res->errmsg}");
            return false;
        }
        Log::error("模板留言失败: {$json_res}");
        return false;
    }

    /**
     * 设置缓存，按需重载
     * @param string $cachename
     * @param mixed $value
     * @param int $expired
     * @return boolean
     */
    protected function setCache($cachename, $value, $expired)
    {
        Redis::set($cachename, $value);
        Redis::expire($cachename, $expired);
        return true;
    }


    /**
     * 获取缓存，按需重载
     * @param string $cachename
     * @return mixed
     */
    protected function getCache($cachename)
    {
        $gtime = Redis::ttl($cachename);
        if ($gtime > 120) {
            $values = Redis::get($cachename);
            return $values;
        }

        return false;
    }

    /**
     * 微信校验敏感词
     * @param string $cachename
     * @return mixed
     */
    public function checkMsg($content = '', $pro_id = '')
    {
        $checkClass = new Check();
        $token = $this->getToken($pro_id);
        $data = $checkClass->msgSecCheck($token, $content);

        if ($data['errcode'] == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 微信校验敏感图片
     * @param string $cachename
     * @return mixed
     */
    public function checkImg($media = null, $pro_id = '')
    {
        $checkClass = new Check();
        $token = $this->getToken($pro_id);
        $data = $checkClass->imgSecCheck($token, $media);
        if ($data['errcode'] == 0) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Created by PhpStorm.
     * ps:保存二维码
     * $img 是二维码base_64流
     */
    function saveimg($img,$path='home')
    {
        $t = time() . rand(1, 999999);
        //生成图片
        $filename = $t . ".jpg";///要生成的图片名字
        $disk = Storage::disk('oss_huike');
        $objectname = getenv('HUIKE_OSS_OBJECTNAME');
//        $folder = 'home';
        $result = $disk->put($objectname . '/' . $path . '/' . $filename, $img); //上传的是流 所以返回的是true 没有具体路径

        if ($result) {
            return $objectname . '/' . $path . '/' . $filename;
        } else {
            return false;
        }
    }

    /**
     * 剪切图片为圆形
     * @param  $picture 图片数据流 比如file_get_contents(imageurl)返回的东东
     * @return 图片数据流
     */
    function yuanImg($picture)
    {

        $src_img = imagecreatefromstring($picture);
        $w = imagesx($src_img);
        $h = imagesy($src_img);
        $w = min($w, $h);
        $h = $w;
        $img = imagecreatetruecolor($w, $h);


        //这一句一定要有
        imagesavealpha($img, true);
        //拾取一个完全透明的颜色,最后一个参数127为全透明
        //$bg = imagecolorallocatealpha($img, 255, 255, 255, 127);

        //白色背景
        $bg = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $bg);
        $r = $w / 2; //圆半径
        $y_x = $r; //圆心X坐标
        $y_y = $r; //圆心Y坐标
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $rgbColor = imagecolorat($src_img, $x, $y);
                if (((($x - $r) * ($x - $r) + ($y - $r) * ($y - $r)) < ($r * $r))) {
                    imagesetpixel($img, $x, $y, $rgbColor);
                }
            }
        }
        /**
         * 如果想要直接输出图片，应该先设header。header("Content-Type: image/png; charset=utf-8");
         * 并且去掉缓存区函数
         */
        //获取输出缓存，否则imagepng会把图片输出到浏览器
        ob_start();
        imagepng($img);
        imagedestroy($img);
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }

    /**
     * Desc: 发送小程序企业微信
     * Author: xinu
     * Time: 2021-04-13 17:47
     * @param $proId
     * @param $workUserId
     * @param $content
     * @return mixed
     */
    public function qySendMiniAppInfo($proId, $workUserId, $content)
    {
        if (!$workUserId) {
            return false;
        }

        $token = $this->getQyToken($proId, 0, 'app');
        $apiStr = 'cgi-bin/message/send?access_token=' . $token;
        $apiUrl = 'https://qyapi.weixin.qq.com/';
        $body = [
            'touser' => $workUserId,
            'msgtype' => 'miniprogram_notice',
            'miniprogram_notice' => $content
        ];
        $res = http_post($body, $apiStr, $apiUrl);
        return json_decode($res, true);
    }


    /**
     * Notes:获取用户风险等级
     * User: haozhen
     * DateTime: 2021/6/3 下午4:56
     * @param $proid
     * @param $openid
     * @return bool|string
     * Uri:
     */
    public function getUserRiskRank($proid, $openid)
    {
        $pro_id = empty($proid) ?  : $proid;

        $pro_info = Project::where('id', $pro_id)->first();

        if (empty($pro_info)) {
            return false;
        }
        $pro_info = $pro_info->toArray();
        $appid = $pro_info['appid'];

        $token = $this->getToken($pro_id,2);

        if (!$token) {
            return false;
        }
        $url = getenv('WXURL') . 'wxa/getuserriskrank?access_token=' . $token;

        $postdata['appid'] = $appid;
        $postdata['openid'] = $openid;
        $postdata['scene'] = 1;
        $postdata['client_ip'] = getClientRealIP(true);

        $ret = $this->https_request($url, $postdata, 'json');
        return $ret;
    }


    /**
     * Desc: 创建企业微信销售二维码
     * Author: haozhen
     * @param $pro_id
     * @param $work_user_id
     * @param $openid
     * @return array
     */
    public function createQrcode($xml, $pro_id)
    {
        if (!$pro_id || !$xml) {
            return false;
        }

        $xml_arr = xmlToArray($xml);

        $openid = $xml_arr['FromUserName'];
        //$work_user_id = $xml_arr['SessionFrom'];


//        $sale = DB::table('sale')->where(
//            [
//                'work_user_id' => '18342429649',
//                'pro_id' => $pro_id
//            ])->first();
//
//        if (isset($sale->qr_code)) {
//
//            $qr_code = $sale->qr_code;
//        } else {
//            $body = [];
//
//            // 获得企业微信accesstoken
//            $WechatHandler = new WechatHandler();
//            $access_token = $WechatHandler->getQyToken($pro_id, '', 'department');
//
//            $apiStr = 'cgi-bin/user/get?access_token=' . $access_token . '&userid=' . $work_user_id;
//            $apiUrl = 'https://qyapi.weixin.qq.com/';
//
//            $res = http_post($body, $apiStr, $apiUrl);
//            $data = json_decode($res);
//
//
//            if ($data->errcode === 0) {
//
//                DB::table('sale')->where('work_user_id', $work_user_id)->update(
//                    [
//                        'qr_code' => $data->qr_code,
//                    ]
//                );
//
//                $qr_code = $data->qr_code;
//            }
//
//        }

        $body['touser'] = $openid;
        $body['msgtype'] = 'text';
        $body['text']['content'] = '12345';
        // 获得企业微信accesstoken
        $WechatHandler = new WechatHandler();
        $access_token = $WechatHandler->getToken($pro_id);

        $apiStr = '/cgi-bin/message/custom/send?access_token=' . $access_token;
        $apiUrl = 'https://api.weixin.qq.com';

        $res = http_post($body, $apiStr, $apiUrl);
        $wxdata = json_decode($res);

        return $wxdata;

    }
}
