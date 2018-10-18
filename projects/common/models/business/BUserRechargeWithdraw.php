<?php

namespace common\models\business;

class BUserRechargeWithdraw extends \common\models\UserRechargeWithdraw
{
    const STATUS_WAIT = 0; //待审核
    const STATUS_ON = 1; //正常
    const STATUS_ERROR = 2; //失败
    const STATUS_NO = 3; //未通过

    public static function getStatus($key = 0)
    {
        $arr = [
            self::STATUS_WAIT => \Yii::t('app', '待审核'),
            self::STATUS_ON => \Yii::t('app', '已审核'),
            self::STATUS_WAIT => \Yii::t('app', '待审核'),
            self::STATUS_NO => \Yii::t('app', '未通过'),
        ];
        if ($key !== "") {
            return isset($arr[$key]) ? $arr[$key] : "";
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
