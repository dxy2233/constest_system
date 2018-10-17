<?php

namespace common\models\business;

use common\models\business\Traits\UserCurrencyTrait;
use yii\behaviors\TimestampBehavior;

class BUserCurrencyFrozen extends \common\models\UserCurrencyFrozen
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
