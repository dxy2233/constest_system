<?php

namespace common\models\business;

class BNodeExtend extends \common\models\NodeExtend
{
    const STATUS_WAIT = 0; //待审核
    const STATUS_ACTIVE = 1; //审核通过
    const STATUS_STOP = 2; //审核失败

    public static function getStatus($key = 0)
    {
        $arr = [
            self::STATUS_WAIT => \Yii::t('app', '未激活'),
            self::STATUS_ACTIVE => \Yii::t('app', '已激活'),
            self::STATUS_STOP => \Yii::t('app', '停用'),
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
