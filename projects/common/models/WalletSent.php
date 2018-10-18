<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%wallet_sent}}".
 *
 * @property integer $id
 * @property integer $currency_id
 * @property string $transaction_number
 * @property string $transaction_id
 * @property integer $type
 * @property string $relate_table
 * @property integer $relate_id
 * @property string $amount
 * @property string $source_address
 * @property string $destination_address
 * @property string $remark
 * @property string $response_data
 * @property integer $status
 * @property integer $create_time
 * @property integer $update_time
 */
class WalletSent extends \common\dzbase\DzModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wallet_sent}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['currency_id', 'type', 'relate_id', 'status', 'create_time', 'update_time'], 'integer'],
            [['amount'], 'number'],
            [['response_data'], 'string'],
            [['transaction_number', 'source_address', 'destination_address', 'remark'], 'string', 'max' => 50],
            [['transaction_id'], 'string', 'max' => 100],
            [['relate_table'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '支付请求记录ID',
            'currency_id' => '货币',
            'transaction_number' => '交易单号',
            'transaction_id' => '交易ID',
            'type' => '类型，1 用户提现，2 充值用户激活，3 用户转入主钱包',
            'relate_table' => '类型关联表',
            'relate_id' => '类型关联表数据ID',
            'amount' => '数量',
            'source_address' => '发送方地址',
            'destination_address' => '接收方地址',
            'remark' => '备注',
            'response_data' => '响应数据',
            'status' => '状态，0 待确认，1 操作成功，2 操作失败',
            'create_time' => '添加时间',
            'update_time' => '修改时间',
        ];
    }
}
