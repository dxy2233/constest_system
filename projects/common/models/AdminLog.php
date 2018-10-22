<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%admin_log}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $route
 * @property string $content
 * @property string $ip
 * @property integer $create_time
 */
class AdminLog extends \common\dzbase\DzModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'create_time'], 'integer'],
            [['content'], 'string'],
            [['route'], 'string', 'max' => 200],
            [['ip'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '用户操作ID',
            'user_id' => '用户ID',
            'route' => '路由',
            'content' => '内容',
            'ip' => 'IP',
            'create_time' => '添加时间',
        ];
    }
}
