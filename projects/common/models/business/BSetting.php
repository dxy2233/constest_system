<?php

namespace common\models\business;

class BSetting extends \common\models\Setting
{
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
     * 自定义查询规则
     * 公用定义查询类
     * @return void
     */
    public static function find()
    {
        return new BaseQuery(get_called_class());
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
