<?php

namespace common\models\business;

class BTypeRuleContrast extends \common\models\TypeRuleContrast
{
    public static $TYPE_TENURE = 1; // 任职
    public static $TYPE_ALL = 0; // 候补
    public static $TYPE_ORDER = 2; // 排名
    
    



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
