<?php

namespace common\models\business;

use yii\behaviors\TimestampBehavior;

class BUserWallet extends \common\models\UserWallet
{
    /**
     * 转换是否为主钱包字段
     *
     * @return void
     */
    public function getIsMainText()
    {
        return (bool) $this->is_main;
    }

    /**
     * 用户钱包
     *  一对多
     * @return void
     */
    public function getUser()
    {
        return $this->hasOne(BUser::className(), ['id' => 'user_id']);
    }

    /**
     * 用户钱包->用户货币 和上面不一样
     *  一对多
     * @return void
     */
    public function getUserCurrencys()
    {
        return $this->hasMany(BUserCurrency::className(), ['id' => 'wallet_id']);
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
