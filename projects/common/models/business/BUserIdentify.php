<?php

namespace common\models\business;

class BUserIdentify extends \common\models\UserIdentify
{


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
