<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%wallet_jingtum}}".
 *
 * @property integer $id
 * @property string $secret
 * @property string $address
 * @property integer $create_time
 */
class WalletJingtum extends \common\dzbase\DzModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wallet_jingtum}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_time'], 'integer'],
            [['secret', 'address'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'secret' => '私钥',
            'address' => '地址',
            'create_time' => '添加时间',
        ];
    }
}
