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
     * @param bool $flag
     * @return string
     * info: 格式化货币小数，将amount格式化为amount.000....（舍去小数位数后）
     */
    public static function formatAmount($amount = '', int $precision = 0, $flag = false)
    {
        $walletPrecision = (int) \Yii::$app->params['wallet_precision'];
        if (!(bool) $precision) {
            $precision = $walletPrecision;
        }
        
        if (empty($amount)) {
            $amount = 0;
        }

        $flagStr = "";
        if($flag) {
            $flagStr = round($amount, 8) > 0 ? "+" : "";
        }

        if ($precision >= 8) {
            return $flagStr.substr(sprintf("%.8f", $amount), 0);
        }

        if ($precision <= 0) {
            return $flagStr.(string) intval($amount);
        }

        return $flagStr.substr(sprintf("%.8f", $amount), 0, -8 + $precision);
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
        if(!$time) {
            return "";
        }
        return date($formate, $time);
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
     * 生成随机字符
     *
     * @param integer $length 字符长度设定
     * @param boolean $strtoupper 是否大写 默认大写输出
     * @return void
     */
    public static function random(int $length = 6, bool $strtoupper = true)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        }

        if ($strtoupper) {
            $string = strtoupper($string);
        }

        return $string;
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
        $keyName = strtolower($name);
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
    /**
     * 手机号验证，可自定义正则
     *
     * @param [type] $mobile
     * @param string $ereg
     * @return void
     */
    public static function validatMobile($mobile, string $ereg = null)
    {
        $ereg = $ereg ?? "/^1[345678]\d{9}$/";
        if (preg_match($ereg, $mobile)) {
            return true;
        }
        return false;
    }
    /**
     * 钱包地址验证，可自定义正则
     *
     * @param [type] $mobile
     * @param string $ereg
     * @return void
     */
    public static function validatewallet($walletAddress, $ereg = null)
    {
        $ereg = $ereg ?? "/^[A-Za-z0-9]+$/";
        if (preg_match($ereg, $walletAddress)) {
            return true;
        }
        return false;
    }

    public static function getWalletTypeList(string $key = null)
    {
        $wallet = \yii\helpers\ArrayHelper::toArray(\Yii::$app->params['wallet']);
        return is_null($key) ? $wallet : $wallet[strtoupper($key)];
    }

    /**
     * 获取默认钱包
     *
     * @param string $key
     * @return void
     */
    public static function getDefaultWallet(string $key = null)
    {
        $walletList = self::getWalletTypeList();
        $data = \yii\helpers\ArrayHelper::getColumn($walletList, function ($element) {
            if ($element['default']) {
                return $element;
            }
        });
        return is_null($key) ? reset($data) : $walletList[strtoupper($key)];
    }
    /**
     * 转换驼峰命名
     *
     * @param [type] $data
     * @param string $separator
     * @return void
     */
    public static function keyCamelize(&$data, string $separator = '_')
    {
        if (is_object($data)) {
            $data = \yii\helpers\ArrayHelper::toArray($data);
        }
        if (is_array($data)) {
            $newData = [];
            foreach ($data as $key => &$value) {
                if (is_object($value)) {
                    $value = \yii\helpers\ArrayHelper::toArray($value);
                }
                if (is_string($key)) {
                    $key = self::camelize($key, $separator);
                }
                if (is_array($value)) {
                    $value = self::keyCamelize($value, $separator);
                }
                $newData[$key] = $value;
            }
            $data = $newData;
        } else {
            $data = self::camelize($data, $separator);
        }
        return $data;
    }

    /**
     * 密码加密 使用字符串加密
     *  长度为 128位
     * @param string $password
     * @param string $secretKey
     * @return void
     */
    public static function encryptPassWordHash(string $password)
    {
        return \Yii::$app->getSecurity()->generatePasswordHash($password);
    }
    /**
     * 密码验证 使用字符串加密
     *  长度为 128位
     * @param string $password
     * @param string $secretKey
     * @return void
     */
    public static function validatePassWordHash(string $password, string $hash = null)
    {
        if (is_null($hash) || is_null($password)) {
            return false;
        }
        return \Yii::$app->getSecurity()->validatePassword($password, $hash);
    }

    /**
     * 密码加密 使用字符串加密
     *  长度为 128位
     * @param string $password
     * @param string $secretKey
     * @return void
     */
    public static function encryptPassWord(string $password, string $secretKey = null)
    {
        if (is_null($secretKey)) {
            $secretKey = isset(\Yii::$app->params['secretKey']) ? \Yii::$app->params['secretKey'] : '';
        }
        
        return \Yii::$app->getSecurity()->encryptByPassword($password, $secretKey);
    }

    /**
     * 密码解密 使用字符串加密
     *  长度为 128位
     * @param string $password
     * @param string $secretKey
     * @return void
     */
    public static function decryptPassWord(string $password, string $secretKey = null)
    {
        if (is_null($secretKey)) {
            $secretKey = isset(\Yii::$app->params['secretKey']) ? \Yii::$app->params['secretKey'] : '';
        }
        var_dump($password, $secretKey, \Yii::$app->getSecurity()->decryptByPassword($password, $secretKey));
        exit;
        return \Yii::$app->getSecurity()->decryptByPassword($password, $secretKey);
    }
    
    /**
     * 字符串转驼峰命名法
     *
     * @param string $data
     * @param string $separator
     * @return void
     */
    public static function camelize(string $data, string $separator = '_')
    {
        $data = $separator. str_replace($separator, " ", $data);
        return ltrim(str_replace(" ", "", ucwords($data)), $separator);
    }

    /**
     * 进制转换
     *
     * @param [type] $data
     * @param integer $frombase
     * @param integer $tobase
     * @param integer $default
     * @return void
     */
    public static function radixConvert($data, int $frombase = 8, int $tobase = 36, int $default = 10000000000)
    {
        $dlen = strlen($default);
        if (is_int($data) && $data > 0) {
            $pad = str_pad($data, $dlen, $default, STR_PAD_LEFT);
            $convert = base_convert($pad, $frombase, $tobase);
            $data = strtoupper($convert);
        } elseif (is_string($data)) {
            echo $data;
            $convert = (int) base_convert($data, $tobase, $frombase);
            echo $convert;

            $data = $convert - $default;
        }
        return $data;
    }
}
