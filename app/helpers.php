<?php

use App\Handlers\WechatHandler;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!function_exists('export_excel')) {
    function export_excel($sheets, $file)
    {
        $AZ = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        ];

        for ($a = 'AA'; $a <= 'AZ'; $a++) {
            $AZ[] = $a;
        }
        for ($a = 'BA'; $a <= 'BZ'; $a++) {
            $AZ[] = $a;
        }
        for ($a = 'CA'; $a <= 'CZ'; $a++) {
            $AZ[] = $a;
        }
        ob_end_clean();
        $spreadsheet = new Spreadsheet();
        $n = 0;
        foreach ($sheets as $sheetTitle => $sheet) {
            $data = array_values($sheet['data']);
            $header = array_values($sheet['header']);
            if (empty($header)) {
                throw new \Exception('excel头不可为空');
            }
            if ($n) {
                $spreadsheet->addSheet(new Worksheet($spreadsheet, (string)$sheetTitle), $n);
            }
            $sheet = $spreadsheet->getSheet($n);
            $sheet->setTitle((string)$sheetTitle);
            $n++;
            foreach ($header as $kIndex => $item) {
                $k = $AZ[$kIndex];
                if (isset($item['width'])) {
// 设置列宽
                    $sheet->getColumnDimension($k)->setWidth((float)$item['width']);
                }
                $sheet->setCellValue("{$k}1", $item['title']);
// 设置标题加粗
                $sheet->getStyle("{$k}1")->getFont()->setBold(true);
            }
            foreach ($data as $k => $row) {
                $rowNum = $k + 2;
                foreach ($header as $xIndex => $v) {
                    $x = $AZ[$xIndex];
                    $sheet->setCellValue("{$x}{$rowNum}", (string)(isset($v['key']) ? ($row[$v['key']] ?? '') : ''));
                    $sheet->getStyle("{$x}{$rowNum}")->setQuotePrefix(true)->setQuotePrefix(true);
                }
            }
        }
        $spreadsheet->setActiveSheetIndex(0);
        $writer = new Xlsx($spreadsheet);
        // $writer->setPreCalculateFormulas(false);
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment;filename=\"{$file}\"");
        header("Cache-Control: max-age=0");
        $writer->save('php://output');
        exit();
    }
}
if (!function_exists('export_excel_file')) {
    function export_excel_file($sheets, $file)
    {
        $AZ = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        ];

        for ($a = 'AA'; $a <= 'AZ'; $a++) {
            $AZ[] = $a;
        }
        for ($a = 'BA'; $a <= 'BZ'; $a++) {
            $AZ[] = $a;
        }
        for ($a = 'CA'; $a <= 'CZ'; $a++) {
            $AZ[] = $a;
        }
        $spreadsheet = new Spreadsheet();
        $n = 0;
        foreach ($sheets as $sheetTitle => $sheet) {
            $data = array_values($sheet['data']);
            $header = array_values($sheet['header']);
            if (empty($header)) {
                throw new \Exception('excel头不可为空');
            }
            if ($n) {
                $spreadsheet->addSheet(new Worksheet($spreadsheet, (string)$sheetTitle), $n);
            }
            $sheet = $spreadsheet->getSheet($n);
            $sheet->setTitle((string)$sheetTitle);
            $n++;
            foreach ($header as $kIndex => $item) {
                $k = $AZ[$kIndex];
                if (isset($item['width'])) {
// 设置列宽
                    $sheet->getColumnDimension($k)->setWidth((float)$item['width']);
                }
                $sheet->setCellValue("{$k}1", $item['title']);
// 设置标题加粗
                $sheet->getStyle("{$k}1")->getFont()->setBold(true);
            }
            foreach ($data as $k => $row) {
                $rowNum = $k + 2;
                foreach ($header as $xIndex => $v) {
                    $x = $AZ[$xIndex];
                    $sheet->setCellValue("{$x}{$rowNum}", (string)($row[$v['key']] ?? ''));
                }
            }
        }
        $spreadsheet->setActiveSheetIndex(0);
        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        $writer->save($file);
        if (is_file($file)) {
            return $file;
        }
        throw new Exception('保存excel失败');
    }
}

if (!function_exists('send_message')) {
    /**
     * Desc: 发送短信
     * User: xinu
     * Time: 2020-08-03 14:27
     * @param $phone
     * @param $signName
     * @param $tempCode
     * @param array $tempPara
     * @param array $conf
     * @param array $ext //扩展字段
     * @return array
     * @throws ClientException
     * @throws ServerException
     */
    function send_message($phone, $signName, $tempCode, $tempPara = [], $conf = [], $ext = []): array
    {
        $config = [
            'accessKeyId' => config('aliyun.sms.accessKeyId'),
            'accessSecret' => config('aliyun.sms.accessKeySecret'),
            'regionId' => config('aliyun.sms.regionId')
        ];
        $config = array_merge($config, $conf);
        $query = [
            'RegionId' => $config['regionId'],
            'PhoneNumbers' => $phone,
            'SignName' => $signName,
            'TemplateCode' => $tempCode,
            'TemplateParam' => is_array($tempPara) ? json_encode($tempPara, JSON_UNESCAPED_UNICODE) : $tempPara,
        ];
        $query = array_merge($query, $ext);
        AlibabaCloud::accessKeyClient($config['accessKeyId'], $config['accessSecret'])
            ->regionId($config['regionId'])
            ->asDefaultClient();

        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => $query,
                ])
                ->request();
            return $result->toArray();
        } catch (ClientException $e) {
            throw $e;
        } catch (ServerException $e) {
            throw $e;
        }
    }
}

if (!function_exists('check_phone_code')) {
    /**
     * Desc: 检查验证码是否正确
     * User: xinu
     * Time: 2020-08-03 14:53
     * @param $phone
     * @param $code
     * @return bool
     */
    function check_phone_code($phone, $code)
    {
        $originalCode = \Illuminate\Support\Facades\Redis::get(\App\Http\Controllers\Api\CommonController::getPhoneKey($phone));
        \Illuminate\Support\Facades\Redis::del(\App\Http\Controllers\Api\CommonController::getPhoneKey($phone));
        return (string)$originalCode === $code;
    }
}


if (!function_exists('getClientRealIP')) {
    function getClientRealIP($useProxy = false)
    {
        if (!$useProxy) {
            //REMOTE_ADDR: 是你的客户端跟你的服务器“握手”时候的IP。如果使用了“匿名代理”，REMOTE_ADDR将显示代理服务器的IP。
            return $_SERVER['REMOTE_ADDR'];
        }
        $ip = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //HTTP_CLIENT_IP 【可以伪造】是代理服务器发送的HTTP头。如果是“超级匿名代理”，则返回none值。同样，REMOTE_ADDR也会被替换为这个代理服务器的IP。
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            //【不可伪造】需要配置，比如nginx代理中 proxy_set_header X-Real-IP $remote_addr;
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //【可以伪造】用户是在哪个IP使用的代理（有可能存在，也可以伪造） 如果存在，取第一个即可
            $proxyIp = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = $proxyIp[0];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}


function dateRange($startDay, $endDay)
{
    $startTime = strtotime($startDay);
    $endTime = strtotime($endDay);
    if ($endTime < $startTime) {
        return [];
    }
    if ($startDay === $endDay) {
        return [$startDay];
    }
    $res = [];
    do {
        $res[] = $startDay;
        $startDay = date('Y-m-d', strtotime($startDay . ' +1 day'));
    } while ($startDay !== $endDay);
    $res[] = $endDay;
    return $res;
}

if (!function_exists('get_date_from_range')) {
    /**
     * User: xinu
     * Time: 2020-05-01 22:45
     * Desc: 获取两个日期间的所有日期 1970-2038
     * Uri :
     * @param $startdate
     * @param $enddate
     * @return array|bool
     */
    function get_date_from_range($startdate, $enddate)
    {

        $stimestamp = strtotime($startdate);
        $etimestamp = strtotime($enddate);
        if ($stimestamp > $etimestamp) return false;
        // 计算日期段内有多少天
        $days = ($etimestamp - $stimestamp) / 86400 + 1;

        // 保存每天日期
        $date = array();

        for ($i = 0; $i < $days; $i++) {
            $date[] = date('Y-m-d', $stimestamp + (86400 * $i));
        }

        return $date;
    }
}

if (!function_exists('get_days_from_range')) {

    /**
     * Desc: 获取两个日期间的所有日期 1970-2038 改良版
     * Author: xinu
     * Time: 2020-08-25 15:10
     * @param $startdate
     * @param $enddate
     * @return array|false
     */
    function get_days_from_range(string $startdate, string $enddate)
    {
        $startDate = date("Y-m-d", strtotime($startdate));
        $endData = date("Y-m-d", strtotime($enddate));
        $stimestamp = strtotime($startDate);
        $etimestamp = strtotime($endData);
        if ($stimestamp > $etimestamp) return false;
        // 计算日期段内有多少天
        $days = ($etimestamp - $stimestamp) / 86400 + 1;

        // 保存每天日期
        $date = array();

        for ($i = 0; $i < $days; $i++) {
            $date[] = date('Y-m-d', $stimestamp + (86400 * $i));
        }

        return $date;
    }
}

if (!function_exists('get_start_end_date')) {
    /**
     * 获取月份起止日期
     * @param $date 时间戳
     * @return array|false
     */
    function get_start_end_date($date)
    {
        //获取本月开始的时间戳
        $beginThismonth = mktime(0, 0, 0, date('m', $date), 1, date('Y', $date));
        //获取本月结束的时间戳
        $endThismonth = mktime(23, 59, 59, date('m', $date), date('t', $date), date('Y', $date));

        $month_first = date("Y-m-d", $beginThismonth);
        $month_end = date("Y-m-d", $endThismonth);

        return [$month_first, $month_end];
    }
}
if (!function_exists('get_img_oss_url')) {
    /**
     * 获取图片oss地址
     * @param string $path
     * @return mixed
     */
    function get_img_oss_url($path = '')
    {
        $url = trim($path);
        //正则匹配http前缀，如果地址没有前缀加上oss前缀
        if (!preg_match('/(http:|https:)/i', $url)) {
            $domain = getenv('HUIKE_OSS_URL');
            $url = $domain . '/' . $url;
        }
        return $url;
    }
}

if (!function_exists('get_file_vod_url')) {
    /**
     * 获取视频地址
     * @param string $path
     * @return mixed
     */
    function get_file_vod_url($path = '')
    {
        $url = trim($path);
        //正则匹配http前缀，如果地址没有前缀加上oss前缀
        if (!preg_match('/(http:|https:)/i', $url)) {
            $domain = getenv('VOD_ACCESS_HOST');
            $url = $domain . '/' . $url;
        }
        return $url;
    }
}

if (!function_exists('get_month_holiday')) {
    /**
     * 获取月份中的节假日
     * @param string $date
     * @return mixed
     */
    function get_month_holiday($date)
    {
        $date = date("Ym", strtotime($date));

        $url = "http://www.easybots.cn/api/holiday.php?m=" . $date;

        $res = get_curl($url);    //json格式，前端需要直接提供

        $res = json_decode($res, true);   //数组格式，方便后端判断

        return $res;
    }
}
if (!function_exists('get_curl')) {
    /**
     * curl
     * @param string $url
     * @return mixed
     */
    function get_curl($url)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/14.0.835.202 Safari/535.1");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); //链接超时时间
            $content = curl_exec($ch);
            curl_close($ch);
//        $content = json_decode($content,true);
            return $content;
        } catch (Exception $error) {
            return "";
        }
    }
}
if (!function_exists('get_remainder_time')) {
    /**
     * 获取两时间戳间隔天数
     * @param string $date
     * @return mixed
     */
    function get_remainder_time($time1, $time2)
    {
        $timediff = $time2 - $time1;
        $days = intval($timediff / 86400);

        return $days;
    }
}
if (!function_exists('deep_in_array')) {
    /**
     * 二维数组某列是否存在某值
     * @param string $date
     * @return mixed
     */
    function deep_in_array($arr, $key, $value)
    {
        foreach ($arr as $k => $v) {
            if (isset($v[$key]) && $v[$key] == $value) return $k;
        }

        return false;
    }
}
if (!function_exists('deep_in_array_format')) {
    /**
     * 二维数组某列是否存在某值
     * @param string $date
     * @return mixed
     */
    function array_value_to_key($arr, $column)
    {
        $res = array_column($arr, $column);

        $new_arr = [];
        foreach ($res as $v) {
            $new_arr[$v] = [];
        }

        return $new_arr;
    }
}
if (!function_exists('day_time_array')) {
    /**
     * 根据时间的开始时间 和结束时间 生成每天的时间信息
     * @param $format_date
     * @return array
     */
    function day_time_array($format_date)
    {
        $start_time = $format_date['start_time'];
        $end_time = $format_date['end_time'];
        $day_list = array();
        $day_key = ceil(diff_between_twodays($start_time, $end_time));  //计算请假跨度天数

        for ($i = 1; $i <= $day_key; $i++) {
            //判断是否是第一天
            $day_info = array("start_time" => '0:00', "end_time" => '23:59');
            $key = $i - 1;
            $week_i = $key;
            if ($i == 1) {
                $data_time = strtotime($start_time);
                $day_info['start_time'] = date("H:i", strtotime($start_time));
            } else {
                $data_time = strtotime("$start_time +$week_i day");
            }
            //判断是否是最后一天 是否是第一天
            if ($i == $day_key) {
                $day_info['end_time'] = date("H:i", strtotime($end_time));
            }
            $day_info['day_time'] = date("Y-m-d", $data_time);
            $day_info['week_time'] = $data_time;
            $day_info['week'] = date("w", $data_time);
            $day_list[] = $day_info;
        }
        return array("day_list" => $day_list);
    }
}
if (!function_exists('diff_between_twodays')) {
    /**
     * 求两个日期之间相差的天数
     * (针对1970年1月1日之后，求之前可以采用泰勒公式)
     * @param string $day1
     * @param string $day2
     * @return number
     */
    function diff_between_twodays($day1, $day2)
    {
        $day1 = strtotime($day1);
        $day2 = strtotime($day2);
        if ($day1 < $day2) {
            $tmp = $day2;
            $day2 = $day1;
            $day1 = $tmp;
        }
        return ($day1 - $day2) / 86400;
    }
}

if (!function_exists('check_fields')) {

    function check_fields(array $arr, array $need_key)
    {
        if (empty($arr)
            || (empty($need_key))
        ) {
            return true;
        }

        foreach ($need_key as $val) {
            if (!isset($arr[$val])) {
                return false;
            }
        }
        return true;
    }
}

if (!function_exists('define_txt_view')) {
    /**
     * 定义格式化，用id做key
     *
     */
    function define_txt_view($arr, $flag_key = 'id')
    {
        $new_arr = [];
        foreach ($arr as $key => $val) {
            $new_arr[$val[$flag_key]] = is_object($val) ? $val->toArray() : $val;
        }
        return $new_arr;
    }
}


if (!function_exists('get_js_json_data')) {
    /**
     * 获得前端js使用json 数据 ， 多维数组
     *
     * @param $arr  从这个数据中取值
     * @param array $need_keys 取哪些key值
     * @param bool $is_hash 是否使用hash 格式，避免json自动排序问题
     * @return array
     */
    function get_js_json_data($arr, $need_keys = [])
    {
        $arr = is_object($arr) ? $arr->toArray() : $arr;
        $tmp = [];
        if (empty($need_keys)) {
            // 取得所有数据
            $tmp = $arr;
        } else {
            foreach ($need_keys as $nv) {
                $tmp[$nv] = $arr[$nv] ?? '';
            }
        }
        return $tmp;
    }
}

if (!function_exists('get_js_json_arr')) {
    /**
     * 获得前端js使用json 数据 ， 多维数组
     *
     * @param $arr  从这个数据中取值
     * @param array $need_keys 取哪些key值
     * @param bool $is_hash 是否使用hash 格式，避免json自动排序问题
     * @return array
     */
    function get_js_json_arr($arr, $need_keys = [], $is_hash = false)
    {
        $new_arr = [];
        foreach ($arr as $key => $val) {
            $tmp = get_js_json_data($val, $need_keys);

            if ($is_hash == true) {
                $new_arr['a' . $key] = $tmp;
            } else {
                $new_arr[] = $tmp;
            }
        }
        return $new_arr;
    }
}
if (!function_exists('month_list')) {
    /**
     * 生成从开始月份到结束月份的月份数组
     * @param int $start 开始时间戳
     * @param int $end 结束时间戳
     */
    function month_list($start, $end)
    {
        if (!is_numeric($start) || !is_numeric($end) || ($end <= $start)) return '';
        $start = date('Y-m', $start);
        $end = date('Y-m', $end);
        //转为时间戳
        $start = strtotime($start . '-01');
        $end = strtotime($end . '-01');
        $i = 0;//http://www.phpernote.com/php-function/224.html

        $d = array();

        while ($start <= $end) {

            //这里累加每个月的的总秒数 计算公式：上一月1号的时间戳秒数减去当前月的时间戳秒数

            $d[$i] = trim(date('Y-m', $start), ' ');

            $start += strtotime('+1 month', $start) - $start;

            $i++;

        }

        return $d;

    }
}

if (!function_exists('check_sign')) {

    /**
     * 加密验证，生成加密串
     */

    function check_sign($timeStamp = '', $randomStr = '', $signature = '', $pro_id = 0, $activity_id = 0, $cid = 0)
    {

        $str = $timeStamp . $randomStr . 'wechat.wemark';
        if ($pro_id) {
            $str .= $pro_id;
        }
        if ($activity_id) {
            $str .= $activity_id;
        }
        if ($cid) {
            $str .= $cid;
        }

        $str = sha1($str);
        $str = md5($str);
        $str = strtoupper($str);

//        echo $signature.'=='.$str;
//        var_dump($signature==$str);
        if ($str != $signature) {
            return ['code' => 400, 'msg' => '签名错误'];
        }
        $model = new \App\Models\ActivitySign();
        //查询签名是否使用过
        $info = $model->where(['pro_id' => $pro_id, 'sign' => $str])->first();
        if ($info) {
            return ['code' => 401, 'msg' => '签名已使用'];
        } else {
            $model->pro_id = $pro_id;
            $model->sign = $str;
            $model->create_time = date('Y-m-d H:i:s');
            $model->save();
            return ['code' => 200, 'msg' => '签名正确'];
        }

    }

    //打印sql语句，放在之前语句之前，继续执行
    if (!function_exists('_sql')) {
        function _sql()
        {
            DB::listen(function ($query) {
                $bindings = $query->bindings;
                $sql = $query->sql;
                foreach ($bindings as $replace) {
                    $value = is_numeric($replace) ? $replace : "'" . $replace . "'";
                    $sql = preg_replace('/\?/', $value, $sql, 1);
                }
                dump($sql);
            });
        }
    }

    //打印sql语句，放在之前语句之前,终止下面执行
    if (!function_exists('_sqldd')) {
        function _sqldd()
        {
            DB::listen(function ($query) {
                $bindings = $query->bindings;
                $sql = $query->sql;
                foreach ($bindings as $replace) {
                    $value = is_numeric($replace) ? $replace : "'" . $replace . "'";
                    $sql = preg_replace('/\?/', $value, $sql, 1);
                }
                dd($sql);
            });
        }
    }
}

if (!function_exists('create_osn')) {
    /**
     * 生成订单号
     * @param string $path
     * @return mixed
     */
    function create_osn()
    {
        return date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }
}

if (!function_exists('get_rand_str')) {
    /**
     * Notes: 随机生成字符串
     * User: kongkong
     * Date: 2020/8/4
     * Time: 15:07
     * @param $length
     * @return string
     */
    function get_rand_str($length)
    {
        $str = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomStr = str_shuffle($str);
        $code = date('Ynj') . substr($randomStr, 0, $length);

        return $code;
    }
}

if (!function_exists('retain_key_shuffle')) {
    /**
     * Notes: 保留键随机打乱数组顺序
     * User: kongkong
     * Date: 2020/8/4
     * Time: 15:07
     */
    function retain_key_shuffle(array &$arr)
    {
        if (!empty($arr)) {
            $key = array_keys($arr);
            shuffle($key);
            foreach ($key as $value) {
                $arr2[$value] = $arr[$value];
            }
            $arr = $arr2;
        }
    }
}
if (!function_exists('array_unset')) {
    /**
     * Notes:根据二维数组某元素值去重
     * User: kongkong
     * Date: 2020/8/13
     * Time: 16:01
     * @param $arr
     * @param $key
     * @return array
     */
    function array_unset($arr, $key)
    {
        //建立一个目标数组
        $res = array();
        foreach ($arr as $value) {
            //查看有没有重复项

            if (isset($res[$value[$key]])) {
                //有：销毁

                unset($value[$key]);

            } else {

                $res[$value[$key]] = $value;
            }
        }
        return $res;

    }
}


if (!function_exists('array_column')) {
    /**
     * array_column 兼容低版本php
     * (PHP < 5.5.0)
     * @param $array
     * @param $columnKey
     * @param null $indexKey
     * @return array
     */
    function array_column($array, $columnKey, $indexKey = null)
    {
        $result = array();
        foreach ($array as $subArray) {
            if (is_null($indexKey) && array_key_exists($columnKey, $subArray)) {
                $result[] = is_object($subArray) ? $subArray->$columnKey : $subArray[$columnKey];
            } elseif (array_key_exists($indexKey, $subArray)) {
                if (is_null($columnKey)) {
                    $index = is_object($subArray) ? $subArray->$indexKey : $subArray[$indexKey];
                    $result[$index] = $subArray;
                } elseif (array_key_exists($columnKey, $subArray)) {
                    $index = is_object($subArray) ? $subArray->$indexKey : $subArray[$indexKey];
                    $result[$index] = is_object($subArray) ? $subArray->$columnKey : $subArray[$columnKey];
                }
            }
        }
        return $result;
    }
}

if (!function_exists('curlPost')) {
    /**
     * curl请求指定url (post)
     * @param $url
     * @param array $data
     * @return mixed
     */
    function curlPost($url, $data = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}


if (!function_exists('curl')) {
    /**
     * curl请求指定url (get)
     * @param $url
     * @param array $data
     * @return mixed
     */
    function curl($url, $data = [])
    {
        // 处理get数据
        if (!empty($data)) {
            $url = $url . '?' . http_build_query($data);
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
}



if (!function_exists('microtime_float')) {
    /**
     * Notes:获取微秒
     * User: haozhen
     */
    function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
}


if (!function_exists('http_post')) {

    /**
     * Notes:GuzzleHttp post
     * User: haozhen
     */

    function http_post($body, $apiStr, $apiUrl)
    {
        $client = new \GuzzleHttp\Client(['base_uri' => $apiUrl]);
        $res = $client->request('POST', $apiStr,
            ['json' => $body,
                'headers' => [
                    'Content-type' => 'application/json',
                    //'Cookie'=> 'XDEBUG_SESSION=PHPSTORM',
                    "Accept" => "application/json"]
            ]);
        $data = $res->getBody()->getContents();

        return $data;
    }
}


if (!function_exists('http_get')) {

    /**
     * Notes:GuzzleHttp get
     * User: haozhen
     */

    function http_get($apiStr, $header, $apiUrl)
    {
        $client = new \GuzzleHttp\Client(['base_uri' => $apiUrl]);
        $res = $client->request('GET', $apiStr, ['headers' => $header]);
        $statusCode = $res->getStatusCode();

        $header = $res->getHeader('content-type');

        $data = $res->getBody();

        return $data;
    }
}

if (!function_exists('request_post')) {


    function request_post($url = '', $param = '')
    {
        if (empty($url) || empty($param)) {
            return false;
        }

        $postUrl = $url;
        $curlPost = $param;
        $curl = curl_init();//初始化curl
        curl_setopt($curl, CURLOPT_URL, $postUrl);//抓取指定网页
        curl_setopt($curl, CURLOPT_HEADER, 0);//设置header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($curl);//运行curl
        curl_close($curl);

        return $data;
    }

}

if (!function_exists('success')) {
    /**
     * $msg   返回提示消息
     * $data  返回数据
     */
    function success($data = [], $code = '200', $msg = '操作成功')
    {
        return response()->json([
            'status' => 'success',
            'code' => $code,
            'message' => $msg,
            'data' => $data,
        ], $code);
    }
}


if (!function_exists('fail')) {
    /**
     * $msg   返回提示消息
     * $data  返回数据
     */
    function fail($data = [], $code = '500', $msg = '操作失败')
    {
        return response()->json([
            'status' => 'fail',
            'code' => $code,
            'message' => $msg,
            'data' => $data,
        ], $code);
    }
}


if (!function_exists('yuanImg')) {
    /**
     * 剪切图片为圆形
     * @param  $picture 图片数据流 比如file_get_contents(imageurl)返回的东东
     * @return 图片数据流
     */
    function circle_img($picture)
    {

        $src_img = imagecreatefromstring($picture);
        $w = imagesx($src_img);
        $h = imagesy($src_img);
        $w = min($w, $h);
        $h = $w;
        $img = imagecreatetruecolor($w, $h);

        //拾取一个完全透明的颜色,最后一个参数127为全透明
        imagealphablending($img, false);
        //imagesavealpha($img, true);

        $transparent = imagecolorallocatealpha($img, 255, 255, 255, 127);
        $r = $w / 2;
        for ($x = 0; $x < $w; $x++)
            for ($y = 0; $y < $h; $y++) {
                $c = imagecolorat($src_img, $x, $y);
                $_x = $x - $w / 2;
                $_y = $y - $h / 2;
                if ((($_x * $_x) + ($_y * $_y)) < ($r * $r)) {
                    imagesetpixel($img, $x, $y, $c);
                } else {
                    imagesetpixel($img, $x, $y, $transparent);
                }
            }
        imagesavealpha($img, true);
        // imagepng($img);///


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
}


if (!function_exists('getChildren')) {
    /**
     * 用递归获取分类信息
     * $data 所有分类
     * $parent_id 父级id
     * $level 层级
     * $result 分好类的数组
     */
    function getChildren($data, $pid = 0)
    {
        $return = array();
        foreach ($data as &$leaf) {
            if ($leaf['pid'] == $pid) {
                foreach ($data as $subleaf) {
                    if ($subleaf['pid'] == $leaf['id']) {
                        $leaf['children'] = getChildren($data, $leaf['id']);
                        break;
                    }
                }
                $return[] = $leaf;
            }
        }
        return $return;
    }
}

if (!function_exists('getChildrenIds')) {
    /**
     * 获取指定级别的所有下级  包括当前的部门id
     *
     * @param string|null $key
     * @param array $replace
     * @param string|null $locale
     * @return string|array|null
     */
    function getChildrenIds($pid, $list)
    {
        $subs = [];//将本部门放到所有级别里--20201104
        foreach ($list as $item) {
            if ($item['pid'] == $pid) {
                if (!in_array($item['id'], $subs)) {
                    $subs[] = $item['id'];
                    $subs = array_merge($subs, getChildrenIds($item['id'], $list));
                }
            }
        }
        return $subs;
    }
}


if (!function_exists('xmlToArray')) {
    function xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }
}


if (!function_exists('socketio')) {
    /*
     * 通知新客户到访
     *
     */
    function socketio($cid, $pro_id, $openno, $estate_id, $phone)
    {

        $str = $cid . '|' . $estate_id . '|' . $pro_id . '|' . $openno . '|' . $phone;

        // 指明给谁推送，为空表示向所有在线用户推送
        $to_uid = 10000;
        // 推送的url地址，上线时改成自己的服务器地址
        $push_api_url = env("SOCKETIO_URL");
        $post_data = array(
            'type' => 'publish',
            'content' => $str,
            'to' => $to_uid,
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $push_api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $return = curl_exec($ch);
        curl_close($ch);
        if ($return == 'ok') {
            return true;
        } else {
            return false;
        }
    }

}

if (!function_exists('timeDateAdd')) {

    //当前时间加多少天
    function timeDateAdd($num)
    {
        if ($num) {
            $date = strtotime(date('Y-m-d', time()));
            $date = strtotime("+$num day", $date);
            $time = strtotime(date('Y-m-d', $date) . ' 23:59:59');
            return $time;
        } else {
            return 0;
        }

    }
}


if (!function_exists('getDateFromRange')) {
    /**
     * 获取指定日期段内每一天的日期
     * @param Date $startdate 开始日期
     * @param Date $enddate 结束日期
     * @return Array
     */
    function getDateFromRange($startdate, $enddate)
    {
        $stimestamp = strtotime($startdate);
        $etimestamp = strtotime($enddate);
        // 计算日期段内有多少天
        $days = ($etimestamp - $stimestamp) / 86400 + 1;
        // 保存每天日期
        $date = array();
        for ($i = 0; $i < $days; $i++) {
            $date[] = date('Y-m-d', $stimestamp + (86400 * $i));
        }
        return $date;
    }
}


if (!function_exists('getRandWeight')) {
    /**
     * 全概率计算(随机销售id)
     *
     * @param array $p array('a'=>0.5,'b'=>0.2,'c'=>0.4)
     * @return string 返回上面数组的key
     */
    function getRandWeight($proArr)
    {
        $result = '';
        //概率数组的总概率精度
        $proSum = array_sum($proArr);
        if ($proSum == 0) {
            $proSum = 1;
        }
        //概率数组循环
        foreach ($proArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($proArr);
        return $result;
    }

    if (!function_exists('generateNum')) {
        //获取唯一序列号
        function generateNum()
        {
            $length = 32;
            //字符组合
            $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $len = strlen($str) - 1;
            $randstr = '';
            for ($i = 0; $i < $length; $i++) {
                $num = mt_rand(0, $len);
                $randstr .= $str[$num];
            }
            return $randstr;
        }
    }

    /**
     * 获取用户的微信风险等级
     */
    if (!function_exists('getUserRisk')) {
        function getUserRisk($pro_id,$openid)
        {
            $WechatHandler = new WechatHandler();
            $ret = $WechatHandler->getUserRiskRank($pro_id, $openid);
            return $ret;
        }
    }

    if (!function_exists('checkSign')) {

        /**
         * 签名验证
         */
        function checkSign($randStr,$time,$cid,$hcid){

            $key = '**s1.#@';
            $randStr = substr($randStr, 2, 6);
            $newSign = sha1($cid . $time .$key. $randStr . $hcid);
            return $newSign;
        }
    }



    if (!function_exists('getpercentage')) {

        /**
         * Desc: 获取两个数字百分比
         */
        function getpercentage($molecule,$denominator){
            if ($molecule == 0 || $denominator== 0) {
                return 0;
            }
            if ($molecule >= $denominator) {
                return 100;
            }
            $res=$molecule/$denominator*100;
            return round($res,2);
        }
    }

    if (!function_exists('get_oss_url')) {

        /**
         * Desc: 获取oss图片完整地址
         */
        function get_oss_url($path = ''){
            $domain = getenv('HUIKE_OSS_URL');

            return get_url($path, $domain);
        }
    }

    if (!function_exists('get_url')) {
        /**
         * 获取vod oss地址
         * @param string $path
         * @param string $domain
         * @return mixed
         */
        function get_url($path = '', $domain = null)
        {
            $url = trim($path);
            if (empty($url)) {
                return $url;
            }
            //正则匹配http前缀，如果地址没有前缀加上vod前缀
            if (!preg_match('/(http:|https:)/i', $url)) {
                $url = $domain ? $domain . $url : $url;
            }
            return $url;
        }
    }

    /*
     * 经典的概率算法，
     * $proArr是一个预先设置的数组，
     * 假设数组为：array(100,200,300，400)，
     * 开始是从1,1000 这个概率范围内筛选第一个数是否在他的出现概率范围之内，
     * 如果不在，则将概率空间，也就是k的值减去刚刚的那个数字的概率空间，
     * 在本例当中就是减去100，也就是说第二个数是在1，900这个范围内筛选的。
     * 这样 筛选到最终，总会有一个数满足要求。
     * 就相当于去一个箱子里摸东西，
     * 第一个不是，第二个不是，第三个还不是，那最后一个一定是。
     * 这个算法简单，而且效率非常高，
     * 这个算法在大数据量的项目中效率非常棒。
     */
    if (!function_exists('get_roll_rand')) {

        function get_roll_rand($proArr)
        {
            $result = '';
            //概率数组的总概率精度
            $proSum = array_sum($proArr);
            //概率数组循环
            foreach ($proArr as $key => $proCur) {
                $randNum = mt_rand(1, $proSum);
                if ($randNum <= $proCur) {
                    $result = $key;
                    break;
                } else {
                    $proSum -= $proCur;
                }
            }
            unset ($proArr);
            return $result;
        }
    }

    /**
     * 取概率，$weight [奖品1=>概率百分之20,奖品2=>百分之30]
     */
    if (!function_exists('get_rand')) {

        function roll($weight = array(), $type = [])
        {
            $roll = rand(1, array_sum($weight));
            // echo $roll."<br>";
            $_tmpW = 0;
            $rollnum = 0;
            foreach ($weight as $k => $v) {
                $min = $_tmpW;
                $_tmpW += $v;
                $max = $_tmpW;
                if ($roll > $min && $roll <= $max) {
                    $rollnum = $k;
                    break;
                }
            }

            $type_value=0;
            if ($type) {
                $type_value = array_rand($type[$rollnum]);
            }

            return ['id' => $rollnum, 'type' => $type_value ? $type_value : 0];
        }
    }



}



/**
 * 多维数组合并
 * @param $array1
 * @param $array2
 * @return array
 */
function array_merge_multiple($array1, $array2)
{
    $merge = $array1 + $array2;
    $data = [];
    foreach ($merge as $key => $val) {
        if (
            isset($array1[$key])
            && is_array($array1[$key])
            && isset($array2[$key])
            && is_array($array2[$key])
        ) {
            $data[$key] = array_merge_multiple($array1[$key], $array2[$key]);
        } else {
            $data[$key] = isset($array2[$key]) ? $array2[$key] : $array1[$key];
        }
    }
    return $data;
}

/**
 * 二维数组排序
 * @param $arr
 * @param $keys
 * @param bool $desc
 * @return mixed
 */
function array_sort($arr, $keys, $desc = false)
{
    $key_value = $new_array = array();
    foreach ($arr as $k => $v) {
        $key_value[$k] = $v[$keys];
    }
    if ($desc) {
        arsort($key_value);
    } else {
        asort($key_value);
    }
    reset($key_value);
    foreach ($key_value as $k => $v) {
        $new_array[$k] = $arr[$k];
    }
    return $new_array;
}

/**
 * 数据导出到excel(csv文件)
 * @param $fileName
 * @param array $tileArray
 * @param array $dataArray
 */
function export_excel($fileName, $tileArray = [], $dataArray = [])
{
    ini_set('memory_limit', '512M');
    ini_set('max_execution_time', 0);
    ob_end_clean();
    ob_start();
    header("Content-Type: text/csv");
    header("Content-Disposition:filename=" . $fileName);
    $fp = fopen('php://output', 'w');
    fwrite($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));// 转码 防止乱码(比如微信昵称)
    fputcsv($fp, $tileArray);
    $index = 0;
    foreach ($dataArray as $item) {
        if ($index == 1000) {
            $index = 0;
            ob_flush();
            flush();
        }
        $index++;
        fputcsv($fp, $item);
    }
    ob_flush();
    flush();
    ob_end_clean();
}

/**
 * 隐藏敏感字符
 * @param $value
 * @return string
 */
function substr_cut($value)
{
    $strlen = mb_strlen($value, 'utf-8');
    if ($strlen <= 1) return $value;
    $firstStr = mb_substr($value, 0, 1, 'utf-8');
    $lastStr = mb_substr($value, -1, 1, 'utf-8');
    return $strlen == 2 ? $firstStr . str_repeat('*', $strlen - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
}

/**
 * 获取当前系统版本号
 * @return mixed|null
 * @throws Exception
 */
function get_version()
{
    static $version = null;
    if ($version) {
        return $version;
    }
    $file = dirname(ROOT_PATH) . '/version.json';
    if (!file_exists($file)) {
        throw new Exception('version.json not found');
    }
    $version = json_decode(file_get_contents($file), true);
    if (!is_array($version)) {
        throw new Exception('version cannot be decoded');
    }
    return $version['version'];
}

/**
 * 获取全局唯一标识符
 * @param bool $trim
 * @return string
 */
function getGuidV4($trim = true)
{
    // Windows
    if (function_exists('com_create_guid') === true) {
        $charid = com_create_guid();
        return $trim == true ? trim($charid, '{}') : $charid;
    }
    // OSX/Linux
    if (function_exists('openssl_random_pseudo_bytes') === true) {
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    // Fallback (PHP 4.2+)
    mt_srand((double)microtime() * 10000);
    $charid = strtolower(md5(uniqid(rand(), true)));
    $hyphen = chr(45);                  // "-"
    $lbrace = $trim ? "" : chr(123);    // "{"
    $rbrace = $trim ? "" : chr(125);    // "}"
    $guidv4 = $lbrace .
        substr($charid, 0, 8) . $hyphen .
        substr($charid, 8, 4) . $hyphen .
        substr($charid, 12, 4) . $hyphen .
        substr($charid, 16, 4) . $hyphen .
        substr($charid, 20, 12) .
        $rbrace;
    return $guidv4;
}

/**
 * 时间戳转换日期
 * @param $timeStamp
 * @return false|string
 */
function format_time($timeStamp)
{
    return date('Y-m-d H:i:s', $timeStamp);
}

/**
 * 左侧填充0
 * @param $value
 * @param int $padLength
 * @return string
 */
function pad_left($value, $padLength = 2)
{
    return \str_pad($value, $padLength, "0", STR_PAD_LEFT);
}

/**
 * 过滤emoji表情
 * @param $text
 * @return null|string|string[]
 */
function filter_emoji($text)
{
    // 此处的preg_replace用于过滤emoji表情
    // 如需支持emoji表情, 需将mysql的编码改为utf8mb4
    return preg_replace('/[\xf0-\xf7].{3}/', '', $text);
}

/**
 * 根据指定长度截取字符串
 * @param $str
 * @param int $length
 * @return bool|string
 */
function str_substr($str, $length = 30)
{
    if (strlen($str) > $length) {
        $str = mb_substr($str, 0, $length);
    }
    return $str;
}




