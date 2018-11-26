<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user_recharge_address}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $currency_id
 * @property string $address
 * @property string $tag
 * @property string $remark
 * @property integer $create_time
 * @property integer $update_time
 */
class UserRechargeAddress extends \common\dzbase\DzModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_recharge_address}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'currency_id', 'create_time', 'update_time'], 'integer'],
            [['address', 'tag'], 'string', 'max' => 200],
            [['remark'], 'string', 'max' => 50],
            [['currency_id', 'address'], 'unique', 'targetAttribute' => ['currency_id', 'address'], 'message' => 'The combination of 积分 and 地址 has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '记录ID',
            'user_id' => '用户ID',
            'currency_id' => '积分',
            'address' => '地址',
            'tag' => '地址标签',
            'remark' => '备注',
            'create_time' => '添加时间',
            'update_time' => '修改时间',
        ];
    }
}
