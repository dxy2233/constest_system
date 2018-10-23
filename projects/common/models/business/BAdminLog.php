<?php

namespace common\models\business;

class BAdminLog extends \common\models\AdminLog
{
    public static $TYPE_LOGIN = 1; // 登录
    public static $TYPE_REGISTER = 2; // 创建

    public static function getType($key = '')
    {
        $arr = [
            static::$TYPE_LOGIN => \Yii::t('app', '登录'),
            static::$TYPE_REGISTER => \Yii::t('app', '创建'),

        ];
        if ($key !== '') {
            return isset($arr[$key]) ? $arr[$key] : '';
        }

        return $arr;
    }

    public static $STATUS_SUCCESS = 1; // 成功
    public static $STATUS_FAIL = 2; // 失败
    public static function getStatus($key = '')
    {
        $arr = [
            static::$STATUS_SUCCESS => \Yii::t('app', '成功'),
            static::$STATUS_FAIL => \Yii::t('app', '失败'),
        ];
        if ($key !== '') {
            return isset($arr[$key]) ? $arr[$key] : '';
        }

        return $arr;
    }

    /**
    * 自定义 label
    * @return array
    */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [

        ]);
    }
}
