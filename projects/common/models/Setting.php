<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%setting}}".
 *
 * @property int $id
 * @property string $name 名称
 * @property string $value 设置值
 * @property string $remark 备注
 * @property int $create_time
 */
class Setting extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%setting}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'value', 'remark', 'create_time'], 'required'],
            [['create_time'], 'integer'],
            [['name', 'value', 'remark'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'value' => '设置值',
            'remark' => '备注',
            'create_time' => 'Create Time',
        ];
    }
}
