<?php

namespace common\models\business;

class BAdminRefreshToken extends \common\models\AdminRefreshToken
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
