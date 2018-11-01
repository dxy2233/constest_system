<?php

namespace common\models\business;

class BVoucher extends \common\models\Voucher
{
    // 定义模型额外字段 用于join 查询调用
    public $use_amount = 0;

    public static $TYPE_RECOMMEND = 1; // 推荐
    
    public function getUseAmountText()
    {
        return (int) $this->use_amount;
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
