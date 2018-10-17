<?php

namespace common\models\business;

class BSetting extends \common\models\Setting
{
    public static $PAYMENT_NUMBER = 'payment_number';
    public static $ORDINARY_NUMBER = 'ordinary_number';
    public static $VOTE_OPEN = 'vote_open';

    // 留备用，没实质性功能
    public function set()
    {
        $data = [];
        for ($i = 1; $i < 10; $i++) {
            $data[$i] = $i;
        }

        $setting = \common\models\business\BSetting::findOne(1);
        $setting->initialize = \yii\helpers\Json::encode($data);
        $setting->save();
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
