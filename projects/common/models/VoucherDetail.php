<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%voucher_detail}}".
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property int $node_id 节点ID
 * @property int $vote_id 投票记录ID
 * @property int $voucher_id 投票券ID
 * @property int $amount 使用数量
 * @property int $create_time 创建时间
 */
class VoucherDetail extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%voucher_detail}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'node_id', 'vote_id', 'amount', 'create_time'], 'required'],
            [['user_id', 'node_id', 'vote_id', 'amount', 'create_time'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'node_id' => '节点ID',
            'vote_id' => '投票记录ID',
            'voucher_id' => '投票券ID',
            'amount' => '使用数量',
            'create_time' => '创建时间',
        ];
    }
}
