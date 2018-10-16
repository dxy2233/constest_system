<?php

namespace common\models\business;

class BUserCurrencyDetail extends \common\models\UserCurrencyDetail
{
    const TYPE_ELECTION = 1; // 选举
    const TYPE_VOTE = 2; // 投票
    const TYPE_UN_VOTE = 3; // 赎回
    
    public static function getType($key)
    {
        $arr = [
            static::TYPE_ELECTION => \Yii::t('app', '选举'),
            static::TYPE_VOTE => \Yii::t('app', '投票'),
            static::TYPE_UN_VOTE => \Yii::t('app', '赎回'),
        ];
        if ($key !== '') {
            return isset($arr[$key]) ? $arr[$key] : '';
        }

        return $arr;
    }
    const STATUS_WAIT_EFFECTIVE = 0; // 未生效
    const STATUS_EFFECTIVE = 1; // 生效
    const STATUS_UN_EFFECTIVE = 2; // 不生效
    
    public static function getStatus($key)
    {
        $arr = [
            static::STATUS_WAIT_EFFECTIVE => \Yii::t('app', '未生效'),
            static::STATUS_EFFECTIVE => \Yii::t('app', '生效'),
            static::STATUS_UN_EFFECTIVE => \Yii::t('app', '不生效'),
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
