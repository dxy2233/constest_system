<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%update_data}}".
 *
 * @property int $id
 * @property string $mobile 手机
 * @property string $grt 贵人通
 * @property string $tt 茶通
 * @property string $bpt 美食通
 * @property int $status 执行状态
 * @property int $create_time
 * @property int $update_time
 */
class UpdateData extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%update_data}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mobile', 'grt', 'tt', 'bpt', 'status', 'create_time', 'update_time'], 'required'],
            [['grt', 'tt', 'bpt'], 'number'],
            [['status', 'create_time', 'update_time'], 'integer'],
            [['mobile'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mobile' => '手机',
            'grt' => '贵人通',
            'tt' => '茶通',
            'bpt' => '美食通',
            'status' => '执行状态',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
