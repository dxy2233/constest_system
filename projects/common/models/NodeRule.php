<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%node_rule}}".
 *
 * @property int $id
 * @property string $name 权限名称
 * @property string $content 权限内容
 * @property int $create_time 添加时间
 */
class NodeRule extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%node_rule}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'content', 'create_time'], 'required'],
            [['content'], 'string'],
            [['create_time'], 'integer'],
            [['name'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '权限名称',
            'content' => '权限内容',
            'create_time' => '添加时间',
        ];
    }
}
