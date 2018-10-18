<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user_recharge_withdraw}}".
 *
 * @property int $id 货币记录ID
 * @property string $order_number 订单号
 * @property int $currency_id 货币
 * @property int $user_id 用户ID
 * @property int $type 类型，1 充值，2 提现
 * @property string $amount 数量
 * @property string $poundage 手续费
 * @property string $source_address 发送方地址
 * @property string $destination_address 接收方地址
 * @property string $tag 地址标签
 * @property string $remark 备注
 * @property string $transaction_id 交易ID
 * @property int $status 状态，0 待确认，1 操作成功，2 操作失败
 * @property string $status_remark 状态备注
 * @property int $audit_admin_id 操作人id
 * @property int $audit_time 操作时间
 * @property int $create_time 添加时间
 * @property int $update_time 修改时间
 */
class UserRechargeWithdraw extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_recharge_withdraw}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['currency_id', 'user_id', 'type', 'status', 'audit_admin_id', 'audit_time', 'create_time', 'update_time'], 'integer'],
            [['amount', 'poundage'], 'number'],
            [['order_number'], 'string', 'max' => 30],
            [['source_address', 'destination_address', 'remark', 'status_remark'], 'string', 'max' => 50],
            [['tag'], 'string', 'max' => 200],
            [['transaction_id'], 'string', 'max' => 100],
            [['order_number'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '货币记录ID',
            'order_number' => '订单号',
            'currency_id' => '货币',
            'user_id' => '用户ID',
            'type' => '类型，1 充值，2 提现',
            'amount' => '数量',
            'poundage' => '手续费',
            'source_address' => '发送方地址',
            'destination_address' => '接收方地址',
            'tag' => '地址标签',
            'remark' => '备注',
            'transaction_id' => '交易ID',
            'status' => '状态，0 待确认，1 操作成功，2 操作失败',
            'status_remark' => '状态备注',
            'audit_admin_id' => '操作人id',
            'audit_time' => '操作时间',
            'create_time' => '添加时间',
            'update_time' => '修改时间',
        ];
    }
}
