<?php

namespace common\models\business;

class BAdminRole extends \common\models\AdminRole
{
    const STATUS_OFF = 0; //停用
    const STATUS_ON = 1; //正常



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
