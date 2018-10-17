<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user_currency_frozen}}".
 *
 * @property string $id 冻结货币明细ID
 * @property int $user_id 用户ID
 * @property int $currency_id 货币
 * @property int $type 类型，1 选举 2 投票
 * @property string $amount 数量
 * @property string $remark 备注
 * @property int $status 状态，0 解冻，1 冻结
 * @property int $unfrozen_time 解冻时间
 * @property int $create_time 添加时间
 * @property int $update_time 修改时间
 */
class UserCurrencyFrozen extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_currency_frozen}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'currency_id', 'type', 'status', 'unfrozen_time', 'create_time', 'update_time'], 'integer'],
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
            'id' => Yii::t('app', '冻结货币明细ID'),
            'user_id' => Yii::t('app', '用户ID'),
            'currency_id' => Yii::t('app', '货币'),
            'type' => Yii::t('app', '类型，1 选举 2 投票'),
            'amount' => Yii::t('app', '数量'),
            'remark' => Yii::t('app', '备注'),
            'status' => Yii::t('app', '状态，0 解冻，1 冻结'),
            'unfrozen_time' => Yii::t('app', '解冻时间'),
            'create_time' => Yii::t('app', '添加时间'),
            'update_time' => Yii::t('app', '修改时间'),
        ];
    }
}
