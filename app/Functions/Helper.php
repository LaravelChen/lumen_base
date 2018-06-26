<?php
/**
 * Created by PhpStorm.
 * User: Rongrong
 * Date: 2017/3/29
 * Time: 15:13
 */

use App\Http\Controllers\Frontend\DataDictionary\Services\DataDictionaryService;
use App\Com\RpcResponse;

/*
 * 生成指定长度随机字符串（大小写英文字母+数字）
 */
function randStr($length)
{
    //字符集
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $str = '';
    for ($i = 0; $i < $length; $i++) {
        $str .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $str;
}

/**
 * 生成混淆码
 *
 * @param int $length 默认八位
 * @return string
 */
function getMixCode($length = 8)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $password;
}


/**
 * 时间转分钟数
 *
 * @param $time  时间 01:10
 * @return mixed  分钟数 70=01*60+10
 */
function timeToMin($time)
{
    $time_a = explode(":", $time);
    return $time_a[0] * 60 + $time_a[1];
}

/**分钟数转时间
 *
 * @param $min 分钟数
 */
function minToTime($min)
{
    $h = floor($min / 60);
    $m = ($min % 60);
    $h = (($h < 10) ? "0" : "") . $h;
    $m = (($m < 10) ? "0" : "") . $m;
    return $h . ":" . $m;
}

/*
 * 将数据输出成JSON格式字符串
 */
function echo_json($object)
{
    echo json_encode($object, JSON_UNESCAPED_UNICODE);
}

/*
 * 获取客户端IP
 */
function clientIP()
{
    $ip = $_SERVER["REMOTE_ADDR"];
    if (substr($ip, 0, 4) == '100.') {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    return $ip;
}

/**
 * @param string $url    图片地址
 * @param string $suffix 拼接后缀
 */
function pictureThumbUrlFormat($url, $suffix)
{
    if (empty($suffix)) {
        //没有后缀直接返回原图
        return $url;
    }
    //获取后缀点的位置
    $flag = strripos($url, ".");
    //截取第一部分
    $start_str = substr($url, 0, $flag);
    //截取第二部分
    $end_str = substr($url, $flag);
    return $start_str . "_r_" . $suffix . $end_str;
}

if (!function_exists('run_time')) {
    function run_time($s = NULL, $type = '')
    {
        $EC_WEEK = unserialize(config("api.EC_WEEK"));
        if ($s != NULL) {
            if ($s > 0) {
                if (empty($type)) {
                    $week2 = $EC_WEEK[date('w', $s)]['cn'];
                    if (date('Y', time()) == date('Y', $s)) {
                        if (date('ymd', time()) == date('ymd', $s)) $d = date('H:i', $s);
                        else if (date('ymd', strtotime("+1 day", time())) == date('ymd', $s)) $d = '明天 ' . date('H:i', $s);
                        else if (date('ymd', strtotime("-1 day", time())) == date('ymd', $s)) $d = '昨天 ' . date('H:i', $s);
                        else $d = date('m-d H:i', $s);
                    } else {
                        $d = date('Y-m-d H:i', $s);
                    }
                } else {
                    $d = date($type, $s);
                }
                $time_text = $d;
                if (empty($type)) $time_text .= '（' . $week2 . '）';
                return $time_text;
            } else {
                return '--';
            }
        } else {
            return '--';
        }
    }
}

/**
 * 设定基数
 *
 * @param $base    原始基数
 * @param $num     被除数
 * @param $date    起始时间戳
 * @param $divisor 除数
 * @return mixed
 */
function getBaseNumber($base, $num, $date, $divisor)
{
    return $base + ceil((time() - $date) / 86400) * (23 + $num % $divisor);
}

/**
 * @param $url         圖片路徑
 * @param $pictureSize 圖片尺寸，格式：60x60
 * @return string
 */
function getPictureSizeUrl($url, $pictureSize = '')
{
    $suffix = strrpos($url, ".");
    $frontPart = substr($url, 0, $suffix);
    $latterPart = substr($url, $suffix);
    $Url = $frontPart . ($pictureSize ? "_r_{$pictureSize}" : '') . $latterPart;
    return $Url;
}

//时间按日分隔线（调取聊天记录时）
if (!function_exists('transDayLine')) {
    function transDayLine($time, $today = array())
    {
        if ($time >= $today[0] && $time <= $today[1]) {
            $result = "Today";
        } elseif ($time < $today[0]) {
            $cha = ceil(($today[0] - $time) / 3600 / 24);
            if ($cha == 1) {
                $result = "Yesterday";
            } else {
                $result = $cha . " days ago";
            }
        } else {
            $cha = ceil(($time - $today[1]) / 3600 / 24);
            if ($cha == 1) {
                $result = "Tomorrow";
            } else {
                $result = $cha . " days later";
            }
        }
        return $result;
    }
}

//数组转换成url参数
if (!function_exists('arrayToUrlQueryStr')) {
    function arrayToUrlQueryStr($data)
    {
        $queryStr = '';
        $i = 0;
        foreach ($data as $k => $v) {
            if ($i) {
                $queryStr .= '&' . $k . '=' . urlencode($v);
            } else {
                $queryStr .= $k . '=' . urlencode($v);
                $i++;
            }
        }
        return $queryStr;
    }
}

/**curl请求地址
 * */
if (!function_exists('curl_get')) {
    function curl_get($url, $n = 3, $header = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书(https)
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);//不检查域名
        if ($n > 0) {
            curl_setopt($ch, CURLOPT_TIMEOUT, $n);
        }
        if (!empty($header)) curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}

if (!function_exists('hasSameNetwork')) {
    function hasSameNetwork($ip1, $ip2)
    {
        $token = array('Token:31735c0d8ae25a6192c4b8f800cc7cc3f4c47bf3');
        $url = 'http://ipapi.ipip.net/find?addr=' . $ip1;
        $ip1Data = json_decode(curl_get($url, 5, $token));

        $url = 'http://ipapi.ipip.net/find?addr=' . $ip2;
        $ip2Data = json_decode(curl_get($url, 5, $token));

        if ($ip1Data && $ip2Data && $ip1Data->ret == 'ok' && $ip2Data->ret == 'ok') {
            //4:运营商,9：行政区划代码
            $ip1network = $ip1Data->data[0] . $ip1Data->data[1] . $ip1Data->data[2] . $ip1Data->data[4];
            $ip2network = $ip2Data->data[0] . $ip2Data->data[1] . $ip2Data->data[2] . $ip2Data->data[4];
            if ($ip1network == $ip2network) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('getOperationByIp')) {
    function getOperationByIp($ip)
    {
        $token = array('Token:31735c0d8ae25a6192c4b8f800cc7cc3f4c47bf3');
        $url = 'http://ipapi.ipip.net/find?addr=' . $ip;
        $ipData = json_decode(curl_get($url, 5, $token));
        return $ipData;
    }
}

if (!function_exists('verifySignature')) {
    /**
     * 验证签名
     *
     * @param array  $data    待验证的数据
     * @param string $secret  密码
     * @param bool   $sortKey 是否要按键名排序，default:不排序
     * @return string error
     */
    function verifySignature($data, $secret, $sortKey = false)
    {
        //expire,time为过期时间
        $expire = isset($data['expire']) ? $data['expire'] : $data['time'];
        if (time() > $expire) {
            return [
                'errCode' => 1,
                'msg' => 'expired'
            ];
        }
        $sign = $data['sign'];
        unset($data['sign']);
        //对数组key排序
        if ($sortKey) {
            ksort($data);
        }
        $values = implode($data);//取出所有的值拼成字符串
        $sgin = md5($values . $secret);//加入密码，生成md5
        if ($sgin == $sign) {
            return null;
        } else {
            return [
                'errCode' => 2,
                'msg' => 'error signature'
            ];
        }
    }
}

if (!function_exists('microTimestamp')) {
    /**
     * 毫秒时间戳
     *
     * @return integer
     */
    function microTimestamp()
    {
        return round(microtime(true) * 1000);
    }
}


function curl_get($url, $timeOut = 0, $header = null)
{
    try {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }

        if ($timeOut > 0) {
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);
        }

        if (!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    } catch (Exception $e) {
        return false;
    }

}


function arrayColumnReindex($ary, $k = 0)
{
    $a = array();
    foreach ($ary as $row) {
        $row = (array)$row;
        if ($k) {
            $a[$row[$k]] = $row;
        } else {
            $a[] = $row;
        }
    }
    return $a;
}


function arrayColumnHasVal($ary, $k)
{
    $a = array();
    foreach ($ary as $row) {
        if (!empty($row[$k]))
            $a[] = $row[$k];
    }
    return $a;
}


function arrayColumnValues($ary, $k)
{
    $a = array();
    foreach ($ary as $row)
        $a[] = $row[$k];
    return array_unique($a);
}


/**
 * 数组格式化为In查询
 *
 * @param      $params
 * @param bool $is_str
 * @return int|string
 */
function dbCreateIn($params, $is_str = false)
{
    if (!$params) {
        return 0;
    }
    $rst = is_string($params) ? "{$params}" : $params;
    if (is_array($params)) {
        $params = array_unique($params);
        $rst = '';
        foreach ($params as $val) {
            $rst .= (is_numeric($val) && !$is_str ? $val : "'{$val}'") . ',';
        }
        $rst = trim($rst, ',');
    }
    return $rst;
}

/**
 * 处理接口返回值 操作成功
 *
 * @param array|object $payload 返回数据
 * @param array $headers        自定义头信息
 * @return \Illuminate\Http\JsonResponse;
 */
function success($payload = null, array $headers = [])
{
    return RpcResponse::success($payload, $headers);
}

/**
 * 处理接口返回值 操作失败
 *
 * @param array         $errorCode 错误码
 * @param string        $message   自定义错误消息
 * @param array         $headers   自定义头信息
 * @return \Illuminate\Http\JsonResponse;
 * @throws \Exception
 */
function error(array $errorCode, string $message = '', $headers = [])
{
    return RpcResponse::error($errorCode, $message, $headers);
}

/**
 * 获取星期翻译
 *
 * @param      $w
 * @param bool $long
 * @return string
 */
function week_name($w, $long = false)
{
    switch ($w) {
        case 0:
            return $long ? 'Sunday' : 'Sun';
        case 1:
            return $long ? 'Monday' : 'Mon';
        case 2:
            return $long ? 'Tuesday' : 'Tue';
        case 3:
            return $long ? 'Wednesday' : 'Wed';
        case 4:
            return $long ? 'Thursday' : 'Thu';
        case 5:
            return $long ? 'Friday' : 'Fri';
        case 6:
            return $long ? 'Saturday' : 'Sat';
    }
}

/**
 * 判断IP是否是国内
 *
 * @param null $ip
 * @return bool
 */
function isInChina($ip = null)
{
    $ip = ($ip) ? $ip : getRealIP(true);
    if (preg_match('/^192\.168\.\d+\.\d+$/', $ip)) return true;
    if ($ip == '127.0.0.1') return true;
    $city = getIpCity($ip);
    if (!$city) return true;
    $zz = "/^(中国|((?:[\x{4e00}-\x{9fa5}]+省)|(?:[\x{4e00}-\x{9fa5}]+(自治|行政)区)|(?:[\x{4e00}-\x{9fa5}]+市)))/u";
    return preg_match($zz, $city) ? true : false;
}

/**
 * 百度API获取IP地址
 *
 * @param null $ip
 * @return bool
 */
function getIpCity($ip = null)
{
    $ip = ($ip) ? $ip : getRealIP();
    $url = "http://opendata.baidu.com/api.php?query={$ip}&co=&resource_id=6006&t=&cb=&format=json&tn=baidu";
    $res = curl_get($url, 5);
    if (preg_match('/\"location\":\"(.+?)\"/', $res, $m)) {
        return iconv('gb2312', 'utf-8', $m[1]);
    } else {
        return '';
    }
}

/**
 * 获取真实IP
 *
 * @param bool|false $mode
 * @return string
 */
if (!function_exists('getRealIP')) {
    function getRealIP($mode = false)
    {
        if ($mode) {
            //获取真实ip
            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                //从HTTP_X_FORWARDED_FOR获取
                $forwardIps = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                //排除内网ip
                foreach ($forwardIps as $ip) {
                    //去除空格
                    $ip = trim($ip);

                    if (preg_match('/^10\./', $ip) || preg_match('/^192\.168/', $ip)) {
                        // A类地址：10.0.0.0--10.255.255.255
                        // C类地址：192.168.0.0--192.168.255.255
                        continue;
                    }

                    $ipFields = explode('.', $ip);
                    if ($ipFields[0] == 172 && $ipFields[1] >= 16 && $ipFields[1] <= 31) {
                        //B类地址：172.16.0.0--172.31.255.255
                        continue;
                    }

                    $realip = $ip;
                    break;
                }
            } else {
                //如果拿不到ip，返回假ip
                //$realip = '1.1.1.1';
                $realip = empty($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER["REMOTE_ADDR"] : $_SERVER['HTTP_X_REAL_IP'];
            }
        } else {
            //获取remote ip
            $realip = $_SERVER["REMOTE_ADDR"];
        }
        return isset($realip) ? $realip : '';
    }
}


if (!function_exists('formatToArray')) {
    function formatToArray($data)
    {
        return method_exists($data, 'toArray') ? $data->toArray()
            : ($data ? json_decode(json_encode($data), true) : array());
    }
}


/**
 * 处理接口返回值
 *
 * @param int          $code    返回码
 * @param string       $message 返回消息
 * @param array|object $data    返回数据
 * @return array
 * @deprecated 请使用 success() error()
 */
function dd_return(int $code, string $message, $data = null)
{
    $response = ['code' => $code, 'message' => $message];

    if (null !== $data) {
        $response['data'] = $data;
    }

    return $response;
}

/**
 * 检测请求是否来自哒哒客户端
 *
 * @return bool
 */
function is_dada_app()
{
    return false !== stripos($_SERVER['HTTP_USER_AGENT'], 'DaDa');
}


/**
 * 隐藏手机中间数位
 * @param $phone
 * @return mixed|string
 */
function hideMobile($phone){
    if (!empty($phone)){
        $str = substr_replace($phone , '****' , 3 , 4);
    } else{
        $str = '';
    }
    return $str;
}

/**
 * 检测哒哒英语客户端是否需要图形验证码
 * iOS 2.3.0 安卓 2.3.1 以上版本需要图形验证码
 *
 * @return bool
 * @deprecated 请使用 Modules\Frontend\Common\Services\CaptchaService::isRequired
 */
function is_require_captcha()
{
    if (!is_dada_app()) {
        return true;
    }

    $ua = $_SERVER['HTTP_USER_AGENT'];

    if (stripos($ua, 'ios')) {
        $version = substr($ua, strripos($ua, '/') + 1);
        return version_compare($version, '2.3.0', '>');
    }

    if (stripos($ua, 'Android')) {
        $version = substr($ua, strripos($ua, '/') + 1);
        return version_compare($version, '2.3.1', '>');
    }

    return true;
}

/**
 * 获取 Yar 客户端
 *
 * @param string $url 服务 url
 * @return Yar_Client
 */
function get_yar_client(string $url)
{
    $url = trim($url, '/');

    return new Yar_Client($url);
}

/**
 * 频次限制
 *
 * @param mixed $key          需要限制的资源
 * @param int   $maxAttempts  单位时间内最大请求次数
 * @param int   $decayMinutes 单位时间 秒
 * @return bool
 */
function dd_rate_limit($key, int $maxAttempts, int $decayMinutes)
{
    $fingerprint = md5(implode('|', [
        $_SERVER['REQUEST_URI'], serialize($key)
    ]));

    $attempt = (int)dd_cache_client()->incr($fingerprint);

    if ($attempt > $maxAttempts) {
        return false;
    }

    if ($attempt === 1) {
        dd_cache_client()->expire($fingerprint, $decayMinutes);
    }

    return true;
}

/**
 * 老师头像
 *
 * @param string $path
 *
 * @return string
 */
function teacherAvatarUrl(string $path)
{
    return $path ? dd_config('site.data_url') . '/' . $path : '';
}


/**
 * 单个文件上传到文件服务器方法
 *
 * @param $url
 * @param $data
 * @param int $timeout
 * @return bool|mixed
 */
function curlPostOneFile($url,$data,$timeout=0){
    $ch = curl_init();
    //这里用特性检测判断php版本,>=5.5
    if(empty($data['file'])){
        return false;
    }

    if (class_exists('\CURLFile')) {
        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
        $data['file'] = new \CURLFile(realpath($data['file']));
    } else {
        //<=5.5
        if (defined('CURLOPT_SAFE_UPLOAD')) {
            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
        }
        $data['file'] = '@' . realpath($data['file']);
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if(!empty($timeout)){
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    }
    // post数据
    curl_setopt($ch, CURLOPT_POST, 1);
    // post的变量
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $output = curl_exec($ch);
    curl_close($ch);
    //打印获得的数据
    return $output;
}

/**
 * 根据生日时间计算年龄
 *
 * @param string $birthday
 * @param int $age
 *
 * @return int
 */
function get_age_by_birthday(string $birthday, int $age = 0)
{
    if ($birthday == '0000-00-00') {
        return $age;
    }

    $survivalTime = strtotime($birthday);

    if ((time() - $survivalTime) > 0) {
        $age = floor((time() - $survivalTime) / (3600 * 24 * 365));
    }

    return $age;
}

/**
 * 获取字典
 *
 * @param string $key
 * @return array
 */
function dd_get_dictionary(string $key)
{
    return json_decode((new DataDictionaryService())->get_dic_by_key($key), true);
}

/**
 * 获取HTTP REFERER
 */
if (!function_exists('get_http_referer')) {
    function get_http_referer()
    {
        return (!isset($_SERVER['HTTP_REFERER']) OR $_SERVER['HTTP_REFERER'] == '') ? '' : trim($_SERVER['HTTP_REFERER']);
    }
}

/**
 * 获取SEO关键词
 */
if (!function_exists('get_seo_keyword')) {
    function get_seo_keyword()
    {
        if (!$referer = get_http_referer()) {
            return false;
        }

        if (strstr($referer, 'baidu.com')) {
            $regular = '/(?:\?|&)(?:wd|word)=(.*?)(?:&|$)/';
        } elseif (strstr($referer, 'sogou.com')) {
            $regular = '/(?:\?|&)keyword=(.*?)(?:&|$)/';
        } else {
            $regular = '/(?:\?|&)q=(.*?)(?:&|$)/';
        }
        preg_match($regular, $referer, $match);

        return addslashes(urldecode($match[1]));
    }
}

