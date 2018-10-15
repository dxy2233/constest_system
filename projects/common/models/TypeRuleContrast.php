<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%type_rule_contrast}}".
 *
 * @property int $id
 * @property int $type_id 分类ID
 * @property int $min_order 最小排名
 * @property int $max_order 最大排名
 * @property int $is_tenure 是否任职
 * @property int $rule_id 权限ID
 * @property int $create_time
 */
class TypeRuleContrast extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%type_rule_contrast}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type_id', 'min_order', 'max_order', 'is_tenure', 'rule_id'], 'required'],
            [['type_id', 'min_order', 'max_order', 'is_tenure', 'rule_id', 'create_time'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type_id' => '分类ID',
            'min_order' => '最小排名',
            'max_order' => '最大排名',
            'is_tenure' => '是否任职',
            'rule_id' => '权限ID',
            'create_time' => 'Create Time',
        ];
    }
}
