<?php

namespace common\models\business;

class BSetting extends \common\models\Setting
{
    public static $PAYMENT_NUMBER = 'payment_number';
    public static $ORDINARY_NUMBER = 'ordinary_number';
    public static $VOTE_OPEN = 'vote_open';



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
