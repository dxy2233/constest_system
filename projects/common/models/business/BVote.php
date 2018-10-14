<?php

namespace common\models\business;

class BVote extends \common\models\Vote
{
    public static $TYPE_ORDINARY = 1; // 普通投票
    public static $TYPE_PAY = 2; // 支付投票
    public static $TYPE_VOUCHER = 3; // 券投票
    public static $TYPE_RECOMMEND = 4; // 推荐投票

    public static function getType($key = '')
    {
        $arr = [
            static::$TYPE_ORDINARY => \Yii::t('app', '普通投票'),
            static::$TYPE_PAY => \Yii::t('app', '支付投票'),
            static::$TYPE_VOUCHER => \Yii::t('app', '券投票'),
            static::$TYPE_RECOMMEND => \Yii::t('app', '推荐投票'),
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
