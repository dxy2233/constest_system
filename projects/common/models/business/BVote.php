<?php

namespace common\models\business;



class BVote extends \common\models\Vote
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
