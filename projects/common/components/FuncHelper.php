<?php
namespace common\components;

use common\models\business\BSetting;

class FuncHelper
{

    /**
     * 加密解密函数
     * @param $string
     * @param string $operation
     * @param string $key
     * @param int $expiry
     * @return bool|string
     */
    public static function authCode($string, $operation = 'DECODE', $key = '', $expiry = 60 * 5)
    {
        $ckey_length = 4;
        $key = md5($key != '' ? $key : \Yii::$app->params['authCodeKey']);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), - $ckey_length)) : '';

        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);

        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for ($i = 0; $i <= 255; $i ++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for ($j = $i = 0; $i < 256; $i ++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $string_length; $i ++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if ($operation == 'DECODE') {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc . str_replace('=', '', base64_encode($result));
        }
    }

    /**
     * 获取上传图片地址
     * @param $imagePath
     * @return bool|string
     */
    public static function getImageUrl($imagePath = "")
    {

        // if ($imagePath) {
        //     $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https:' : 'http:';
        //     $imgAddress = explode(':', \Yii::$app->params['imgAddress'], 2);
        //     $imgAddress[0] = $http_type;
        //     $imgAddress[2] = $imagePath;
        //     $imagePath = implode($imgAddress);
        // }
        if ($imagePath) {
            $imagePath = \Yii::$app->params['imgAddress'].$imagePath;
        }

        return $imagePath;
    }

    /**
     * 获取附件地址
     * @param $attachmentPath
     * @return bool|string
     */
    public static function getAttachmentUrl($attachmentPath = "")
    {
        $path = '';
        if ($attachmentPath) {
            $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https:' : 'http:';
            $path = explode(':', \Yii::$app->params['ossAddress'], 2);
            $path[0] = $http_type;
            $path[2] = $attachmentPath;
            $attachmentPath = implode($path);
        }

        return $attachmentPath;
    }

    /**
     * @param string $amount
     * @param int $precision
     * @return string
     * info: 格式化货币小数，将amount格式化为amount.000....（舍去小数位数后）
     */
    public static function formatCurrencyAmount($amount = '', $precision = 8)
    {
        if (empty($amount)) {
            $amount = 0;
        }
        if ($precision >= 8) {
            return substr(sprintf("%.8f", $amount), 0);
        }

        if ($precision <= 0) {
            return (string) intval($amount);
        }

        return substr(sprintf("%.8f", $amount), 0, -8 + $precision);
    }

    /**
     * @param string $amount
     * @return string
     * info: 格式化人民币，将amount格式化为amount.00(四舍五入)
     */
    public static function formatCNY($amount = '')
    {
        $baseNum = 0;
        if (!empty($amount)) {
            $baseNum = $amount;
        }

        return round(sprintf('%3f', $baseNum), 2);
    }

    /**
     * @return string
     * interface info : 生成订单号
     */
    public static function generateOrderCode()
    {
        return date("YmdHis", time()) . mt_rand(100, 999);
    }

    /**
     * @return string
     * interface info : 生成付款序列码
     */
    public static function generatePaySequenceCode()
    {
        return mt_rand(100000, 999999);
    }

    /**
    * @return string
    * interface info : 生成钱包支付订单号
    */
    public static function generateWalletSentTransNum()
    {
        return "Trans".date("YmdHis", time()) . mt_rand(100000, 999999);
    }

    /**
     * socket消息推送
     * @param $act 事件
     * @param $target 推送对象：all 全部用户，group 用户组， single 用户组下对应uid
     * @param $group 用户组
     * @param $uid 用户id
     * @param $content 内容
     * @return FuncResult
     */
    public static function sendSocketMsg($act, $target, $group, $uid, $content)
    {
        $url = \Yii::$app->params['SOCKET_URL'].':'.\Yii::$app->params['SOCKET_SERVER_PORT'];
        $data = array(
            'type' => 'publish',
            'act' => $act,
            'target' => $target,
            'group' => $group,
            'uid' => $uid,
            'content' => $content,
        );
        $post = array();
        $post['data'] = self::authcode(serialize($data), 'ENCODE');
        $res = self::request($url, '', $post);

        if (strpos($res, 'ok') === 0) {
            return new FuncResult(0, $res);
        } else {
            return new FuncResult(1, $res);
        }
    }

    /**
     * socket请求
     * @param $params 参数
     * @return FuncResult
     */
    public static function sendSocketRequest($params)
    {
        $url = \Yii::$app->params['SOCKET_URL'].':'.\Yii::$app->params['SOCKET_SERVER_PORT'];
        $data = $params;
        $post = array();
        $post['data'] = self::authcode(serialize($data), 'ENCODE');
        $res = self::request($url, '', $post);

        if (strpos($res, 'ok') === 0) {
            $content = str_replace("ok,", "", $res);
            return new FuncResult(0, '请求成功', $content);
        } else {
            return new FuncResult(1, $res);
        }
    }

    public static function request($url, $compress = '', $post_data = '', $header = array(), $cookiejar = '', $timeout = 10)
    {
        $ch = curl_init();
        if (stripos($url, "https://") !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $def_header = array(
            'Connection' => 'keep-alive',
            'Cache-Control' => 'no-cache',
            'Pragma' => 'no-cache',
            'User-Agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null,
            'Accept' => isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : null,
            'Referer' => null,
            'Accept-Encoding' => 'gzip,deflate,sdch',
            'Accept-Language' => 'en-US,zh-CN;q=0.8,zh;q=0.6',
            'Accept-Encoding' => 'GBK,utf-8;q=0.7,*;q=0.3'
        );
        if (! empty($header)) {
            foreach ($header as $key => $value) {
                $def_header[$key] = $value;
            }
        }
        $new_header = array();
        foreach ($def_header as $key => $value) {
            if ($value !== null) {
                $new_header[] = $key . ": " . $value;
            }
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $new_header);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        if ($compress) {
            curl_setopt($ch, CURLOPT_ENCODING, $compress);
        }
        if ($post_data) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        }
        if ($cookiejar) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiejar);
        }
        if (is_file($cookiejar)) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiejar);
        }
        $content = curl_exec($ch);
        $status = curl_getinfo($ch);
        curl_close($ch);
        $fls = false;
        if (intval($status["http_code"]) == 200) {
            unset($status);
            return $content;
        } else {
            unset($status);
            return $fls;
        }
    }

    /** 判断数组键值是否存在
     * Determine if the given key exists in the provided array.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string|int  $key
     * @return bool
     */
    public static function arrayExists($array, $key)
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }
    /** 指定键名比较计算数组的交集
     * Get a subset of the items from the given array.
     *
     * @param  array  $array
     * @param  array|string  $keys
     * @return array
     */
    public static function arrayOnly($array, $keys)
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }

    /** 排除数组指定键值
     * Remove one or many array items from a given array using "dot" notation.
     *
     * @param  array  $array
     * @param  array|string  $keys
     * @return void
     */
    public static function arrayForget(&$array, $keys)
    {
        $original = &$array;

        $keys = (array) $keys;

        if (count($keys) === 0) {
            return;
        }

        foreach ($keys as $key) {
            // if the exact key exists in the top-level, remove it
            if (static::arrayExists($array, $key)) {
                unset($array[$key]);

                continue;
            }
            var_dump($array);
            exit;
            $parts = explode('.', $key);

            // clean up before each pass
            $array = &$original;

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part];
                } else {
                    continue 2;
                }
            }

            unset($array[array_shift($parts)]);
        }
        return $array;
    }
    /**
     * 时间戳转换时间
     */
    public static function formateDate($time = null, $formate = 'Y-m-d H:i:s')
    {
        $time = $time ?? time();
        return date($formate, $time);
    }

    /**
     * 获取当天时间戳
     */
    public static function todayTime(): array
    {
        return [
            'startTime' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
            'endTime' => mktime(23, 59, 59, date('m'), date('d'), date('Y'))
        ];
    }
    /**
     * 获取当天时间
     */
    public static function filterTime(string $type): array
    {
        switch ($type) {
            case 'week':
                $formate = [
                    'startTime' => date('Y-m-d', strtotime("-6 day")),
                    'endTime' => date('Y-m-d', strtotime("+1 day"))
                ];
                break;
                case 'mounth':
                    $formate = [
                        'startTime' => date('Y-m-d', strtotime("-30 day")),
                        'endTime' => date('Y-m-d', strtotime("+1 day"))
                    ];
                break;
            default:
                $formate = [
                    'startTime' => date('Y-m-d'),
                    'endTime' => date('Y-m-d', strtotime("+1 day"))
                ];
        }
        return $formate;
    }
    /**
     * 指定时间转换时间戳
     */
    public static function toTime($data = [])
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (empty($value)) {
                    $data[$key] = null;
                    continue;
                }
                $data[$key] = strtotime($value);
            }
        } else {
            if (empty($data)) {
                return null;
            }
            $data = strtotime($data);
        }
        
        return $data;
    }

    public static function screenTime(array $model = [])
    {
        $time = \yii\helpers\Html::submitButton('当天', ['class' => 'btn btn-danger', 'name' => 'filterTime', 'value' => 'today']).PHP_EOL;
        $time .= \yii\helpers\Html::submitButton('一周', ['class' => 'btn btn-danger', 'name' => 'filterTime', 'value' => 'week']).PHP_EOL;
        $time .= \yii\helpers\Html::submitButton('一个月', ['class' => 'btn btn-danger', 'name' => 'filterTime', 'value' => 'mounth']).PHP_EOL;
        return $time;
    }


    /**
     * @param int $length
     * @return string
     * info: 生成钱包密码
     */
    public static function generateWalletPassword($length = 8)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        }

        return $password;
    }

    /**
     * @param array $arr
     * @param string $key_str
     * @param int $num
     * @param int $order
     * @return array
     * info:  对二维数组排序
     */
    
    public static function arr_sort($arr, $key_str, $num = 0, $order = 0)
    {
        $count = count($arr);
        if ($count <= 1) {
            return $arr;
        } else {
            if ($num == 0 || $num >= $count) {
                // 完整排序
                $item_key = ceil($count/2);
                $item_val = $arr[$item_key][$key_str];
                $first_arr = $second_arr = $three_arr = array();
                foreach ($arr as $key => $value) {
                    if ($order == 0) {
                        if ($value[$key_str] > $item_val) {
                            $first_arr[] = $value;
                        } elseif ($value[$key_str] < $item_val) {
                            $second_arr[] = $value;
                        } else {
                            $three_arr[] = $value;
                        }
                    } else {
                        if ($value[$key_str] < $item_val) {
                            $first_arr[] = $value;
                        } elseif ($value[$key_str] > $item_val) {
                            $second_arr[] = $value;
                        } else {
                            $three_arr[] = $value;
                        }
                    }
                }
                // 进行递归
                if (count($first_arr)>0) {
                    $first_arr = self::arr_sort($first_arr, $key_str, 0, $order);
                }
                if (count($second_arr)>0) {
                    $second_arr = self::arr_sort($second_arr, $key_str, 0, $order);
                }
                return array_merge($first_arr, $three_arr, $second_arr);
            } else {
                // 只取部分
                for ($i = 0;$i < $num; $i++) {
                    // 读取数组第一个成员作参考
                    $item = reset($arr)[$key_str];
                    $item_key = key($arr);
                    // 选出最大/最小值
                    foreach ($arr as $key => $val) {
                        if ($order == 0 && $val[$key_str] >= $item) {
                            $item = $val[$key_str];
                            $item_key = $key;
                        } elseif ($order == 1 && $val[$key_str] <= $item) {
                            $item = $val[$key_str];
                            $item_key = $key;
                        }
                    }
                    $new_arr[] = $arr[$item_key];
                    unset($arr[$item_key]);
                }
                return $new_arr;
            }
        }
    }

    /**
     * @param array $arr
     * @param int $length
     * @return array
     * info:  去掉数字末尾的0
     */
    public static function array_delendnum($arr, $length = -1)
    {
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                $arr[$key] = self::array_delendnum($value, $length);
            } elseif (is_numeric($value)) {
                //去掉末尾的数字0并使用字符串类型避免丢失精度
                $v = (string)($value + 0);
                if ($length != -1) {
                    $v = sprintf("%.".$length."f", $v);
                }
                $arr[$key] = $v;
            }
        }

        return $arr;
    }

    /**
     * @param $arr
     * @return array|null|\yii\db\ActiveRecord[]
     * info : 将数组中的bid替换成code
     */

    public static function BidToCode($arr)
    {

        //取出所有币种bid->code对应关系
        
        $name_res = BBid::find()
        ->from(BBid::tableName().' c')
        ->join("INNER JOIN", "gr_currency a", "c.base_currency_id = a.id")
        ->join("INNER JOIN", "gr_currency b", "c.exchange_currency_id = b.id")
        ->select(['c.id','a.code as acode','b.code as bcode'])->asArray()->all();

        foreach ($name_res as $key => $value) {
            $id_code[$value['id']] = strtoupper($value['acode']). '/'. strtoupper($value['bcode']);
        }
        //替换目标数组中的bid
        foreach ($arr as $key => $value) {
            $arr[$key]['code'] = $id_code[$value['bid']];
            unset($arr[$key]['bid']);
        }

        return $arr;
    }
    /**
     * 设置缓存前缀
     *
     * @param [type] $prefix
     * @return string
     */
    public static function cachePrefix($prefix) :string
    {
        return $prefix ?? 'gr';
    }
    /**
     * 设置获取缓存名称
     *
     * @param string $name
     * @param string $prefix
     * @return string
     */
    public static function cacheName(string $name) :string
    {
        $request = \Yii::$app->request;
        $isLang = isset($request->getBodyParams) && in_array($request->getBodyParams('lang'), \Yii::$app->params['languageList']);
        $keyName = $isLang ? $request->getBodyParams('lang') : \Yii::$app->params['defaultLanguage'];
        $keyName .= '.';
        $keyName .= strtolower($name);
        self::setCacheList($keyName);
        return $keyName;
    }
    /**
     * 记录缓存值
     *
     * @param string $keyName
     * @param string $cacheKey
     * @return void
     */
    public static function setCacheList(string $keyName = null, string $cacheKey = 'cache.list')
    {
        if (!isset(\Yii::$app->cache->redis)) {
            return [];
        }
        $redis = \Yii::$app->cache->redis;
        if (is_null($keyName)) {
            return $redis->sscan($cacheKey, 100);
        } else {
            return $redis->sadd($cacheKey, $keyName);
        }
    }
    /**
     * 删除指定缓存
     *
     * @param string $keys
     * @param string $prefix
     * @return void
     */
    public static function cacheFlush(string $keys = null)
    {
        $cache = \Yii::$app->cache;
        if (is_null($keys)) {
            list(, $keys) = self::setCacheList(null);
        } elseif (!is_array($keys)) {
            $keys[] = $keys;
        }
        foreach ($keys as $key) {
            $cache->delete($key);
            if (!isset(\Yii::$app->cache->redis)) {
                continue;
            }
            $redis = $cache->redis;
            $redis->srem('cache.list', $key);
        }
        return count($keys);
    }
}
