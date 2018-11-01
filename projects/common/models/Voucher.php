<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%voucher}}".
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property int $node_id 赠送节点ID
 * @property int $voucher_num 券总量
 * @property int $use_voucher 可用数量
 * @property int $create_time
 */
class Voucher extends \common\dzbase\DzModel
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%voucher}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'node_id', 'voucher_num'], 'required'],
            [['user_id', 'node_id', 'voucher_num', 'create_time'], 'integer'],
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
            'node_id' => '赠送节点ID',
            'voucher_num' => '券总量',
            'give_user_id' => '赠送人ID',
            'type' => '派发类型',
            'remark' => '备注',
            'create_time' => 'Create Time',
        ];
    }
}
