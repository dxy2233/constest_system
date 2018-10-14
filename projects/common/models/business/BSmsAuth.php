<?php

namespace common\models\business;

use common\traits\SmsType;

class BSmsAuth extends \common\models\SmsAuth
{
    // 下面一个 trait 记录所有发送短信类型ID
    use SmsType;
    
    public static $STATUS_UNUSED = 0;

    public static $STATUS_USED = 1;


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
