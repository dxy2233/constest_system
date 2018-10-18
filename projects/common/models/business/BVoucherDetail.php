<?php

namespace common\models\business;

class BVoucherDetail extends \common\models\VoucherDetail
{
    // 投票记录 投票
    public function getVote()
    {
        return $this->hasOne(BVote::className(), ['id' => 'vote_id']);
    }

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
