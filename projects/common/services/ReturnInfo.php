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
        // 排除控制台调用报错
        if (method_exists(\Yii::$app->request, 'post')) {
            // 多语言翻译
            $post = \Yii::$app->request->post();
            $get = \Yii::$app->request->get();
            if (!empty($post['lang'])) {
                $language = $post['lang'];
            } elseif (!empty($post['lang'])) {
                $language = $get['lang'];
            }
            if (!empty($language) && in_array($language, \Yii::$app->params['languageList'])) {
                $msg = \Yii::t('app', $msg);
            } else {
                // 根据ip地址切换语言
            }
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
