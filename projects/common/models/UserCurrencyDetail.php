<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user_currency_detail}}".
 *
 * @property string $id
 * @property integer $user_id
 * @property integer $currency_id
 * @property integer $type
 * @property string $relate_table
 * @property integer $relate_id
 * @property string $amount
 * @property string $remark
 * @property integer $status
 * @property integer $effect_time
 * @property integer $create_time
 * @property integer $update_time
 */
class UserCurrencyDetail extends \common\dzbase\DzModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_currency_detail}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'currency_id', 'type', 'relate_id', 'status', 'effect_time', 'create_time', 'update_time'], 'integer'],
            [['amount'], 'number'],
            [['relate_table'], 'string', 'max' => 30],
            [['remark'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '货币明细ID',
            'user_id' => '用户ID',
            'currency_id' => '货币',
            'type' => '类型，1 转入 2 提现 4 投票 5 手续费',
            'relate_table' => '类型关联表',
            'relate_id' => '类型关联表数据ID',
            'amount' => '增减数量',
            'remark' => '备注',
            'status' => '状态，0 待生效，1 已生效，2 不生效',
            'effect_time' => '生效时间',
            'create_time' => '添加时间',
            'update_time' => '修改时间',
        ];
    }
}
