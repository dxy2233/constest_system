<?php

namespace common\models\business;

class BUserVoucher extends \common\models\UserVoucher
{

    /**
     * 用户
     *  一对一
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
