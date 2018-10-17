<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%sms_template}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $type
 * @property string $content
 * @property integer $status
 * @property string $outer_template_id
 * @property integer $create_time
 */
class SmsTemplate extends \common\dzbase\DzModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sms_template}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['type', 'status', 'create_time'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['content'], 'string', 'max' => 300],
            [['outer_template_id'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '模板名称',
            'type' => '类型',
            'content' => '模板内容',
            'status' => '状态 1 : 正常 0：禁用',
            'outer_template_id' => '外部模板ID',
            'create_time' => '添加时间',
        ];
    }
}
