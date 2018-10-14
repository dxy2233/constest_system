<?php

namespace common\models\business;



class BNodeType extends \common\models\NodeType
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
