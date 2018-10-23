<?php

namespace common\models\business;

class BUserLog extends \common\models\UserLog
{
    public static $TYPE_LOGIN = 1; // 登录
    public static $TYPE_REGISTER = 2; // 创建
    public static $TYPE_ALERT_TRANS_PWD = 3; // 修改交易密码
    public static $TYPE_ALERT_MOBILE = 4; // 修改手机号码
    public static $TYPE_CHECK_TRANS_PWD = 5; // 校验交易密码
    public static $TYPE_RESET_TRANS_PWD = 6; // 重置交易密码
    public static function getType($key = '')
    {
        $arr = [
            static::$TYPE_LOGIN => \Yii::t('app', '登录'),
            static::$TYPE_REGISTER => \Yii::t('app', '创建'),
            static::$TYPE_ALERT_TRANS_PWD => \Yii::t('app', '修改交易密码'),
            static::$TYPE_ALERT_MOBILE => \Yii::t('app', '修改手机号码'),
            static::$TYPE_CHECK_TRANS_PWD => \Yii::t('app', '校验交易密码'),
            static::$TYPE_RESET_TRANS_PWD => \Yii::t('app', '重置交易密码'),
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
