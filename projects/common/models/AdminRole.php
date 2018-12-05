<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%admin_role}}".
 *
 * @property int $id
 * @property string $name 角色名
 * @property string $rule_list 权限列表
 * @property int $status 状态 0：禁用 1：启用
 */
class AdminRole extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%admin_role}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rule_list'], 'string'],
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '角色名',
            'rule_list' => '权限列表',
            'status' => '状态 0：禁用 1：启用',
        ];
    }
}
