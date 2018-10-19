<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%history}}".
 *
 * @property int $id
 * @property int $node_id 节点ID
 * @property int $node_type 节点类型ID
 * @property string $username 用户名
 * @property string $node_name 节点名
 * @property int $vote_number 票数
 * @property int $people_number 支持人数
 * @property int $is_tenure 0:未任职 1:已任职
 * @property int $update_number 更新次数
 * @property int $create_time
 */
class History extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%history}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['node_id', 'username', 'node_name', 'vote_number', 'people_number', 'is_tenure'], 'required'],
            [['node_id','vote_number', 'people_number', 'is_tenure', 'create_time'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'node_id' => Yii::t('app', '节点ID'),
            'node_type' => Yii::t('app', '节点类型ID'),
            'username' => Yii::t('app', '用户名'),
            'node_name' => Yii::t('app', '节点名'),
            'vote_number' => Yii::t('app', '票数'),
            'people_number' => Yii::t('app', '支持人数'),
            'is_tenure' => Yii::t('app', '0:未任职 1:已任职'),
            'update_number' => Yii::t('app', '更新次数'),
            'create_time' => Yii::t('app', 'Create Time'),
        ];
    }
}
