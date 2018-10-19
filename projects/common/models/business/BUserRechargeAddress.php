<?php

namespace common\models\business;

class BUserRechargeAddress extends \common\models\UserRechargeAddress
{
    public function getCurrency()
    {
        return $this->hasOne(BCurrency::className(), ['id' => 'currency_id']);
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
