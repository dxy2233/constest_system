<?php
/**
 * Created by PhpStorm.
 * User: zhangyawei-mac
 * Date: 16/7/15
 * Time: 上午9:22
 */

namespace common\services;

/**
 * 函数返回信息
 * Class ReturnInfo
 * @package common\models
 */
class ReturnInfo
{
    public static $CODE_SUCCESS = [0, '成功'];


    public $code = 0;
    public $msg = '';
    public $content = '';

    public static $staticPropertyMap = null;

    public function __construct($code = 0, $msg = '', $content = '')
    {
        if (is_array($code)) {
            $this->code = $code[0];
        } else {
            $this->code = $code;
        }
        
        if (!YII_DEBUG && $this->code) {
            $content = 'error';
        }

        $this->msg = $msg;
        $this->content = $content;
    }

    /**
     * 通过反射得到所有的静态code的映射
     * @return array|null
     */
    public static function getStaticProperties()
    {
        if (self::$staticPropertyMap == null) {
            $reflection = new \ReflectionClass('common\models\ReturnInfo');
            $propertyMap = [];
            foreach ($reflection->getStaticProperties() as $propertyName => $property) {
                if (strpos($propertyName, 'CODE_') === 0) {
                    $propertyMap[$property[0]] = $property[1];
                }
            }
            self::$staticPropertyMap = $propertyMap;
        }

        return self::$staticPropertyMap;
    }

    public function getCodeMsg()
    {
        return self::getStaticProperties()[$this->code];
    }
}
