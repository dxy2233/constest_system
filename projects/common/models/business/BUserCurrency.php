<?php

namespace common\models\business;

use yii\behaviors\TimestampBehavior;

class BUserCurrency extends \common\models\UserCurrency
{
    

    /**
     * 用户货币 ->货币详情
     *  一对一
     * @return void
     */
    public function getCurrency()
    {
        return $this->hasOne(BCurrency::className(), ['id' => 'currency_id']);
    }

    /**
    * 用户货币 -> 钱包
    *  一对一
    * @return void
    */
    public function getWallet()
    {
        return $this->hasOne(BUserwallet::className(), ['id' => 'currency_id']);
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
