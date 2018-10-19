<?php

namespace common\models\business;

use common\models\business\Traits\UserCurrencyTrait;

class BUserCurrencyDetail extends \common\models\UserCurrencyDetail
{
    use UserCurrencyTrait;

    public static $STATUS_EFFECT_WAIT = 0; // 待生效
    public static $STATUS_EFFECT_SUCCESS = 1; // 已生效
    public static $STATUS_EFFECT_FAIL = 2; // 不生效

    public static function getStatus(int $key = null)
    {
        $arr = [
            static::$STATUS_EFFECT_WAIT => \Yii::t('app', '审核中'),
            static::$STATUS_EFFECT_SUCCESS => \Yii::t('app', '成功'),
            static::$STATUS_EFFECT_FAIL => \Yii::t('app', '失败'),
        ];
        if (!is_null($key)) {
            return isset($arr[$key]) ? $arr[$key] : null;
        }

        return $arr;
    }


    public static $TYPE_INCOME = 1;
    public static $TYPE_WITHDRAW = 2;
    public static $TYPE_VOTE = 4;
    public static $TYPE_POUNDAGE = 5;
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
