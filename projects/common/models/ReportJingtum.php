<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%report_jingtum}}".
 *
 * @property string $id
 * @property string $currency
 * @property string $source_address
 * @property string $destination_address
 * @property string $type
 * @property string $amount
 * @property string $fee
 * @property string $hash
 * @property integer $date
 * @property string $result
 * @property string $raw_data
 * @property integer $is_update
 * @property integer $is_handle
 * @property integer $create_time
 * @property integer $update_time
 */
class ReportJingtum extends \common\dzbase\DzModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%report_jingtum}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['amount', 'fee'], 'number'],
            [['date', 'is_update', 'is_handle', 'create_time', 'update_time'], 'integer'],
            [['raw_data'], 'string'],
            [['currency', 'type'], 'string', 'max' => 20],
            [['source_address', 'destination_address'], 'string', 'max' => 50],
            [['hash', 'result'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '交易订单ID',
            'currency' => '积分',
            'source_address' => '发送方地址',
            'destination_address' => '接收方地址',
            'type' => '分类',
            'amount' => '交易数量',
            'fee' => '手续费',
            'hash' => '交易ID',
            'date' => '交易添加时间',
            'result' => '交易结果',
            'raw_data' => '原始数据',
            'is_update' => '是否继续更新，0 否，1 是',
            'is_handle' => '是否处理，0 未处理，1 充值，2 提现',
            'create_time' => '添加时间',
            'update_time' => '修改时间',
        ];
    }
}
