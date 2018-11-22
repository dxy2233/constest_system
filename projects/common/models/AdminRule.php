<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%admin_rule}}".
 *
 * @property int $id
 * @property string $url 路由地址
 * @property string $name 权限名称
 * @property int $parent_id 上级权限ID
 */
class AdminRule extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%admin_rule}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id'], 'integer'],
            [['url', 'name'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'url' => '路由地址',
            'name' => '权限名称',
            'parent_id' => '上级权限ID',
        ];
    }
}
