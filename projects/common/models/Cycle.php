<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%cycle}}".
 *
 * @property int $id
 * @property int $cycle_start_time 竞选开始时间
 * @property int $cycle_end_time 竞选截止时间
 * @property int $tenure_start_time 任职时间
 * @property int $tenure_end_time 到期时间
 * @property int $create_time
 * @property int $update_time
 */
class Cycle extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%cycle}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cycle_start_time', 'cycle_end_time', 'tenure_start_time', 'tenure_end_time'], 'required'],
            [['cycle_start_time', 'cycle_end_time', 'tenure_start_time', 'tenure_end_time', 'create_time', 'update_time'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cycle_start_time' => '竞选开始时间',
            'cycle_end_time' => '竞选截止时间',
            'tenure_start_time' => '任职时间',
            'tenure_end_time' => '到期时间',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
