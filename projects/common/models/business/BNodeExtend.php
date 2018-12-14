<?php

namespace common\models\business;



class BNodeExtend extends \common\models\NodeExtend
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
