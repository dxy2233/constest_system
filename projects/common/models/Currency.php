<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%currency}}".
 *
 * @property int $id 货币ID
 * @property string $code 编码
 * @property string $name 标题
 * @property string $summary 摘要
 * @property int $status 货币状态：0 下架，1 正常
 * @property int $sort 排序号
 * @property int $create_time 添加时间
 * @property int $update_time 修改时间
 */
class Currency extends \common\dzbase\DzModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%currency}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wallet_id', 'code'], 'required'],
            [['summary'], 'string'],
            [['status', 'sort', 'is_address_tag', 'recharge_status', 'recharge_amount_precision', 'recharge_confirmation', 'withdraw_status',
                'withdraw_amount_precision', 'withdraw_confirmation','otc_status', 'create_time', 'update_time'], 'integer'],
            [['recharge_min_amount', 'withdraw_min_amount'], 'number'],
            [['code', 'name'], 'string', 'max' => 20],
            [['withdraw_max_amount'], 'string', 'max' => 200],
            [['code'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '货币ID',
            'code' => '编码',
            'name' => '标题',
            'summary' => '摘要',
            'status' => '货币状态：0 下架，1 正常',
            'sort' => '排序号',
            'is_address_tag' => '是否有地址标签，0 无，1 有',
            'recharge_status' => '充值状态：0 不可充值，1 可充值',
            'recharge_min_amount' => '充值最小数量',
            'recharge_amount_precision' => '充值数量精度',
            'recharge_confirmation' => '充值确认数',
            'withdraw_status' => '提现状态：0 不可提现，1 可提现',
            'withdraw_min_amount' => '提现最小数量',
            'withdraw_max_amount' => '提现最大数量（等级制）',
            'withdraw_amount_precision' => '提现数量精度',
            'withdraw_confirmation' => '提现确认数',
            'create_time' => '添加时间',
            'update_time' => '修改时间',
            'first' => '提现等级一',
            'second' => '提现等级二',
            'otc_status' => '法币交易， 0 不参与，1 参与',
        ];
    }
}
