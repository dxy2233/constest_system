<?php

namespace common\models\business;

class BAdminAccessToken extends \common\models\AdminAccessToken
{
    public function generateAccessToken()
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