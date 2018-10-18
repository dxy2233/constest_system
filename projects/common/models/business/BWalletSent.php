<?php

namespace common\models\business;



class BWalletSent extends \common\models\WalletSent
 {

    public static $TYPE_WITHDRAW = 1; //提币
    public static $TYPE_ACTIVE = 2; //钱包激活
    public static $TYPE_TO_MAIN = 3; //钱包资产转到主钱包

    public static $STATUS_WAIT = 0; // 待确认
    public static $STATUS_SUCCESS = 1; // 操作成功
    public static $STATUS_FAIL = 2; // 操作失败


    /**
    * 自定义 label
    * @return array
    */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),[

        ]);
    }
}
