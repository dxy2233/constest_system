<?php

namespace common\models\business;



class BIetPush extends \common\models\IetPush
 {


    const TENURE_WAIT = 0; //未推送
    const TENURE_YES = 1; //推送成功
    const TENURE_NO = 2; //推送失败


    public static function getStatus($key = 0)
    {
        $arr = [
            self::TENURE_NO => \Yii::t('app', '推送失败'),
            self::TENURE_YES => \Yii::t('app', '推送成功'),
            self::TENURE_WAIT => \Yii::t('app', '未推送'),

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
        return array_merge(parent::attributeLabels(),[

        ]);
    }
}
