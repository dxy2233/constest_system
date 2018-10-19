<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user_voucher}}".
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property int $position_amount 持券总量
 * @property int $surplus_amount 剩余数量
 * @property int $use_amount 使用数量
 * @property int $create_time
 * @property int $update_time
 */
class UserVoucher extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_voucher}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'position_amount', 'surplus_amount', 'use_amount', 'create_time', 'update_time'], 'required'],
            [['user_id', 'position_amount', 'surplus_amount', 'use_amount', 'create_time', 'update_time'], 'integer'],
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
            'position_amount' => '持券总量',
            'surplus_amount' => '剩余数量',
            'use_amount' => '使用数量',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
