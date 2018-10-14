<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user_currency_detail}}".
 *
 * @property string $id 货币明细ID
 * @property int $user_id 用户ID
 * @property int $currency_id 货币
 * @property int $type 类型，1 选举 2 投票
 * @property string $amount 增减数量
 * @property string $remark 备注
 * @property int $status 状态，0 待生效，1 已生效，2 不生效
 * @property int $effect_time 生效时间
 * @property int $create_time 添加时间
 * @property int $update_time 修改时间
 */
class UserCurrencyDetail extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_currency_detail}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'currency_id', 'type', 'status', 'effect_time', 'create_time', 'update_time'], 'integer'],
            [['amount'], 'number'],
            [['remark'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '货币明细ID',
            'user_id' => '用户ID',
            'currency_id' => '货币',
            'type' => '类型，1 选举 2 投票',
            'amount' => '增减数量',
            'remark' => '备注',
            'status' => '状态，0 待生效，1 已生效，2 不生效',
            'effect_time' => '生效时间',
            'create_time' => '添加时间',
            'update_time' => '修改时间',
        ];
    }
}
