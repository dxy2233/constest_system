<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user_access_token}}".
 *
 * @property int $id token ID
 * @property string $client_id 应用ID
 * @property int $user_id 用户ID
 * @property string $access_token 访问token
 * @property int $expire_time 过期时间
 * @property int $create_time 添加时间
 * @property int $update_time 修改时间
 */
class AdminAccessToken extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%admin_access_token}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'expire_time', 'create_time', 'update_time'], 'integer'],
            [['client_id'], 'string', 'max' => 32],
            [['access_token'], 'string', 'max' => 40],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'token ID',
            'client_id' => '应用ID',
            'user_id' => '用户ID',
            'access_token' => '访问token',
            'expire_time' => '过期时间',
            'create_time' => '添加时间',
            'update_time' => '修改时间',
        ];
    }
}
