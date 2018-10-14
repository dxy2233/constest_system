<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%vote}}".
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property int $node_id 节点ID
 * @property int $vote_number 投票数量
 * @property string $consume 消耗
 * @property int $type 投票类型
 * @property int $status 状态 1：正常 0：已赎回
 * @property int $create_time 创建时间
 */
class Vote extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%vote}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'node_id', 'vote_number', 'consume', 'type', 'create_time'], 'required'],
            [['user_id', 'node_id', 'vote_number', 'type', 'status', 'create_time'], 'integer'],
            [['consume'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'node_id' => '节点ID',
            'vote_number' => '投票数量',
            'consume' => '消耗',
            'type' => '投票类型',
            'status' => '状态 1：正常 0：已赎回',
            'create_time' => '创建时间',
        ];
    }
}
