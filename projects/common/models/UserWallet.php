<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user_wallet}}".
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property string $wallet 钱包CODE
 * @property string $address 钱包地址
 * @property string $secret 钱包私钥
 * @property int $create_time 创建时间
 * @property int $update_time 修改时间
 */
class UserWallet extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_wallet}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'wallet', 'address', 'create_time', 'update_time'], 'required'],
            [['user_id', 'create_time', 'update_time'], 'integer'],
            [['wallet'], 'string', 'max' => 50],
            [['address', 'secret'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', '用户ID'),
            'wallet' => Yii::t('app', '钱包CODE'),
            'address' => Yii::t('app', '钱包地址'),
            'secret' => Yii::t('app', '钱包私钥'),
            'create_time' => Yii::t('app', '创建时间'),
            'update_time' => Yii::t('app', '修改时间'),
        ];
    }
}
