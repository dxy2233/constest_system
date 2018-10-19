<?php

namespace common\models\business;

class BTypeRuleContrast extends \common\models\TypeRuleContrast
{
    public static $TYPE_ORDER = 0;
    public static $TYPE_TENURE = 1;
    public static $TYPE_ALL = 2;



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
