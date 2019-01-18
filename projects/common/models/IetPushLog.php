<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%iet_push_log}}".
 *
 * @property int $id
 * @property string $push_type 推送类型
 * @property string $push_data 推送数据
 * @property string $response 响应数据
 * @property int $status 推送状态 1 推送成功 2 推送失败
 * @property int $relate_id 推送关联数据ID
 * @property int $create_time 推送时间
 */
class IetPushLog extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%iet_push_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['push_data', 'response'], 'required'],
            [['push_data', 'response'], 'string'],
            [['status', 'relate_id', 'create_time'], 'integer'],
            [['push_type'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'push_type' => '推送类型',
            'push_data' => '推送数据',
            'response' => '响应数据',
            'status' => '推送状态 1 推送成功 2 推送失败',
            'relate_id' => '推送关联数据ID',
            'create_time' => '推送时间',
        ];
    }
}
