<?php

namespace common\models\business;



class BVoucher extends \common\models\Voucher
 {





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
