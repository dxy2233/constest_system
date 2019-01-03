<?php

namespace common\models\business;

class BNodeTransfer extends \common\models\NodeTransfer
{
    const STATUS_INACTIVE = 0; //待审核
    const STATUS_ACTIVE = 1; //正常
    const STATUS_FAIL = 2; //审核未通过

    public static function getStatus($key = '')
    {
        $arr = [
            static::STATUS_ACTIVE => \Yii::t('app', '审核成功'),
            static::STATUS_INACTIVE => \Yii::t('app', '待审核'),
            static::STATUS_FAIL => \Yii::t('app', '审核失败'),
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
