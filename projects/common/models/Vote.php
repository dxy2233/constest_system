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
            [['user_id', 'node_id', 'vote_number', 'type', 'create_time', 'unit_code'], 'required'],
            [['user_id', 'node_id', 'vote_number', 'type', 'status', 'create_time'], 'integer'],
            [['consume'], 'number'],
            [['unit_code'], 'string', 'max' => 50],
        ];
    }



    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', '用户ID'),
            'node_id' => Yii::t('app', '节点ID'),
            'vote_number' => Yii::t('app', '投票数量'),
            'consume' => Yii::t('app', '消耗'),
            'type' => Yii::t('app', '投票类型'),
            'status' => Yii::t('app', '状态 1：正常 0：已赎回'),
            'unit_code' => Yii::t('app', '消费单位'),
            'undo_time' => Yii::t('app', '撤消时间'),
            'create_time' => Yii::t('app', '创建时间'),
        ];
    }
}
