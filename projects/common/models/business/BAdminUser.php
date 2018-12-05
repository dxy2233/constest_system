<?php

namespace common\models\business;

class BAdminUser extends \common\models\AdminUser
{
    const STATUS_OFF = 0; //停用
    const STATUS_ON = 1; //正常

    public static function getStatus($key)
    {
        $arr = [
            self::STATUS_OFF => \Yii::t('app', '停用'),
            self::STATUS_ON => \Yii::t('app', '正常'),
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
