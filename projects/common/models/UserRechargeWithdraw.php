<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user_recharge_withdraw}}".
 *
 * @property integer $id
 * @property string $order_number
 * @property integer $currency_id
 * @property integer $user_id
 * @property integer $type
 * @property string $amount
 * @property string $poundage
 * @property string $source_address
 * @property string $destination_address
 * @property string $tag
 * @property string $remark
 * @property string $transaction_id
 * @property integer $status
 * @property string $status_remark
 * @property integer $audit_admin_id
 * @property integer $audit_time
 * @property integer $create_time
 * @property integer $update_time
 */
class UserRechargeWithdraw extends \common\dzbase\DzModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_recharge_withdraw}}';
    }

    /**
     * @inheritdoc
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
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '积分记录ID',
            'order_number' => '订单号',
            'currency_id' => '积分',
            'user_id' => '用户ID',
            'mobile' => '手机号码',
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
