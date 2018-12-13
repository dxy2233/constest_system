<?php

namespace common\models\business;

class BNodeUpgrade extends \common\models\NodeUpgrade
{
    const STATUS_WAIT = 0; //待审核
    const STATUS_ACTIVE = 1; //审核通过
    const STATUS_FAIL = 2; //审核失败

    public static function getStatus($key = 0)
    {
        $arr = [
            self::STATUS_WAIT => \Yii::t('app', '待审核'),
            self::STATUS_ACTIVE => \Yii::t('app', '已审核'),
            self::STATUS_FAIL => \Yii::t('app', '审核失败'),
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
