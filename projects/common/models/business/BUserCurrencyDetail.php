<?php

namespace common\models\business;

use common\models\business\Traits\UserCurrencyTrait;

class BUserCurrencyDetail extends \common\models\UserCurrencyDetail
{
    use UserCurrencyTrait;

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
