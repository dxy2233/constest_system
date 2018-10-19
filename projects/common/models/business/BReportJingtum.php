<?php

namespace common\models\business;



class BReportJingtum extends \common\models\ReportJingtum
 {

    public static $IS_UPDATE_YES = 1; //继续更新
    public static $IS_UPDATE_NO = 0; //停止更新

    public static $IS_HANDLE_NO = 0; //未处理
    public static $IS_HANDLE_RECHARGE = 1; //充值处理
    public static $IS_HANDLE_WITHDRAW = 2; //提现处理



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
