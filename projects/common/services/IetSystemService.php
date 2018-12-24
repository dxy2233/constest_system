<?php
namespace common\services;

use yii\base\Exception;

class IetSystemService extends ServiceBase
{
    protected $key = '';
    protected $signType = 'MD5';
    protected $values = [];
    
    /**
     * 生成签名
    */
    public function MakeSign()
    {
        //签名步骤一：按字典序排序参数
        ksort($this->values);
        $string = $this->ToUrlParams();
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".$this->key();
        //签名步骤三：MD5加密或者HMAC-SHA256
        if ($this->signType == "MD5") {
            $string = md5($string);
        } else {
            throw new Exception("签名类型不支持！");
        }
        
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 格式化参数格式化成url参数
     */
    public function ToUrlParams()
    {
        $buff = "";
        foreach ($this->values as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }
}
