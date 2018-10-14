<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%unvote}}".
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property int $node_id 节点ID
 * @property int $vote_number 投票数量
 * @property int $voteid 投票ID
 * @property int $type 投票类型
 * @property int $create_time 创建时间
 */
class Unvote extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%unvote}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'node_id', 'vote_number', 'voteid', 'type', 'create_time'], 'required'],
            [['user_id', 'node_id', 'vote_number', 'voteid', 'type', 'create_time'], 'integer'],
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
            'voteid' => '投票ID',
            'type' => '投票类型',
            'create_time' => '创建时间',
        ];
    }
}
