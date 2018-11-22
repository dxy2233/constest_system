<?php

namespace common\models\business;

class BAdminUser extends \common\models\AdminUser
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
