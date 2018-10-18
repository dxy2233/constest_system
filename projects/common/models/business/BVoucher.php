<?php

namespace common\models\business;

class BVoucher extends \common\models\Voucher
{

    // 投票记录 用户
    public function getUser()
    {
        return $this->hasOne(BUser::className(), ['id' => 'user_id']);
    }

    // 投票记录节点
    public function getNode()
    {
        return $this->hasOne(BNode::className(), ['id' => 'node_id']);
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
