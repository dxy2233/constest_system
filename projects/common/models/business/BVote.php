<?php

namespace common\models\business;

class BVote extends \common\models\Vote
{
    // 可赎回投票类型
    const IS_REVOKE = [1];

    // 新增 赎回中状态
    const STATUS_INACTIVE_ING = 2;

    const TYPE_ORDINARY = 1; // 持有投票
    const TYPE_PAY = 2; // 支付投票
    const TYPE_VOUCHER = 3; // 券投票

    public static function getType($key = '')
    {
        $arr = [
            static::TYPE_ORDINARY => \Yii::t('app', '持有投票'),
            static::TYPE_PAY => \Yii::t('app', '支付投票'),
            static::TYPE_VOUCHER => \Yii::t('app', '券投票'),
        ];
        if ($key !== '') {
            return isset($arr[$key]) ? $arr[$key] : '';
        }

        return $arr;
    }

    public static function getStatus($key = '')
    {
        $arr = [
            static::STATUS_ACTIVE => \Yii::t('app', '投出'),
            static::STATUS_INACTIVE => \Yii::t('app', '已赎回'),
            static::STATUS_INACTIVE_ING => \Yii::t('app', '赎回中'),
        ];
        if ($key !== '') {
            return isset($arr[$key]) ? $arr[$key] : '';
        }

        return $arr;
    }

    /**
     * 用户关联
     *  一对一
     * @return void
     */
    public function getUser()
    {
        return $this->hasOne(BUser::className(), ['id' => 'user_id']);
    }

    /**
     * 节点关联
     *  一对一
     * @return void
     */
    public function getNode()
    {
        return $this->hasOne(BNode::className(), ['id' => 'node_id']);
    }

    /**
     * 节点关联
     *  一对多
     * @return void
     */
    public function getHistorys()
    {
        return $this->hasMany(BHistory::className(), ['node_id' => 'node_id']);
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
