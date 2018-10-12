<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user_refresh_token}}".
 *
 * @property int $id 自增id
 * @property string $client_id 应用ID
 * @property int $user_id 用户ID
 * @property string $refresh_token 刷新token
 * @property int $expire_time 过期时间
 * @property int $create_time 添加时间
 * @property int $update_time 修改时间
 */
class UserRefreshToken extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_refresh_token}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'expire_time', 'create_time', 'update_time'], 'integer'],
            [['client_id'], 'string', 'max' => 32],
            [['refresh_token'], 'string', 'max' => 40],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增id',
            'client_id' => '应用ID',
            'user_id' => '用户ID',
            'refresh_token' => '刷新token',
            'expire_time' => '过期时间',
            'create_time' => '添加时间',
            'update_time' => '修改时间',
        ];
    }
}
