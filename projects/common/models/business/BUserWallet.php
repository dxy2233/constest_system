<?php

namespace common\models\business;

use yii\behaviors\TimestampBehavior;

class BUserWallet extends \common\models\UserWallet
{

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
    * 自定义 label
    * @return array
    */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [

        ]);
    }
}
