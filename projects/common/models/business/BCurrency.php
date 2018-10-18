<?php

namespace common\models\business;

class BCurrency extends \common\models\Currency
{

     /**
     * 用户钱包
     *  一对多
     * @return void
     */
    public function getCurrency()
    {
        return $this->hasMany(BUserCurrency::className(), ['currency_id' => 'id']);
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
