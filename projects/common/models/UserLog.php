<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user_log}}".
 *
 * @property int $id 用户操作ID
 * @property int $user_id 用户ID
 * @property string $client_id 应用ID
 * @property int $type 类型，1 登录、2 修改登录密码、3 修改交易密码
 * @property string $content 内容
 * @property int $status 状态，1 成功，2 失败
 * @property string $ip IP
 * @property int $create_time 添加时间
 */
class UserLog extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'status', 'create_time'], 'integer'],
            [['client_id'], 'string', 'max' => 32],
            [['content'], 'string', 'max' => 50],
            [['ip'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', '用户操作ID'),
            'user_id' => Yii::t('app', '用户ID'),
            'client_id' => Yii::t('app', '应用ID'),
            'type' => Yii::t('app', '类型，1 登录、2 修改登录密码、3 修改交易密码'),
            'content' => Yii::t('app', '内容'),
            'status' => Yii::t('app', '状态，1 成功，2 失败'),
            'ip' => Yii::t('app', 'IP'),
            'create_time' => Yii::t('app', '添加时间'),
        ];
    }
}
