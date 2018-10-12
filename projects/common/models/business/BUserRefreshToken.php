<?php

namespace common\models\business;

class BUserRefreshToken extends \common\models\UserRefreshToken
{
    public function generateRefreshToken()
    {
        return \Yii::$app->security->generateRandomString();
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
