<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user_currency}}".
 *
 * @property int $id 记录ID
 * @property int $user_id 用户ID
 * @property int $currency_id 积分
 * @property string $position_amount 持仓数量
 * @property string $frozen_amount 冻结数量
 * @property string $use_amount 可用数量
 * @property int $create_time 添加时间
 * @property int $update_time 修改时间
 */
class UserCurrency extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_currency}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'currency_id', 'create_time', 'update_time'], 'integer'],
            [['position_amount', 'frozen_amount', 'use_amount'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', '记录ID'),
            'user_id' => Yii::t('app', '用户ID'),
            'currency_id' => Yii::t('app', '积分'),
            'position_amount' => Yii::t('app', '持仓数量'),
            'frozen_amount' => Yii::t('app', '冻结数量'),
            'use_amount' => Yii::t('app', '可用数量'),
            'create_time' => Yii::t('app', '添加时间'),
            'update_time' => Yii::t('app', '修改时间'),
        ];
    }
}
