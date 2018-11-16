<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%update_log}}".
 *
 * @property int $id
 * @property string $table_name 表名
 * @property string $field_name 字段名
 * @property string $new_data 新数据
 * @property string $old_data 旧数据
 * @property int $create_time
 */
class UpdateLog extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%update_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['table_name', 'field_name', 'new_data', 'old_data', 'create_time'], 'required'],
            [['new_data', 'old_data'], 'string'],
            [['create_time'], 'integer'],
            [['table_name', 'field_name'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'table_name' => '表名',
            'field_name' => '字段名',
            'new_data' => '新数据',
            'old_data' => '旧数据',
            'create_time' => 'Create Time',
        ];
    }
}
