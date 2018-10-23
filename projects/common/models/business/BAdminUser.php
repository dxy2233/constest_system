<?php

namespace common\models\business;

class BAdminUser extends \common\models\AdminUser
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
