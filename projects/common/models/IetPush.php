<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%iet_push}}".
 *
 * @property int $id
 * @property string $push_name 推送名称
 * @property string $push_type 推送类型
 * @property string $push_data 推送数据
 * @property string $response 响应数据
 * @property int $status 推送状态 0 未推送 1 推送成功 2 推送失败
 * @property string $relate_table 推送关联数据表
 * @property int $relate_id 推送关联数据ID
 * @property int $create_time 推送时间
 * @property int $update_time 更新时间
 */
class IetPush extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%iet_push}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['push_data'], 'required'],
            [['push_data', 'response'], 'string'],
            [['status', 'create_time', 'update_time'], 'integer'],
            [['push_name'], 'string', 'max' => 255],
            [['push_type'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'push_name' => Yii::t('app', '推送名称'),
            'push_type' => Yii::t('app', '推送类型'),
            'push_data' => Yii::t('app', '推送数据'),
            'response' => Yii::t('app', '响应数据'),
            'status' => Yii::t('app', '推送状态 0 未推送 1 推送成功 2 推送失败'),
            'create_time' => Yii::t('app', '推送时间'),
            'update_time' => Yii::t('app', '更新时间'),
        ];
    }
}
