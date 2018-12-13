<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%node_extend}}".
 *
 * @property int $id
 * @property string $name 节点名称
 * @property int $type_id 节点类型ID
 * @property string $type_name 节点类型名称
 * @property string $quota 节点配额
 * @property string $mobile 节点手机号
 * @property int $status 节点状态
 * @property string $company 所属公司
 * @property int $create_time 创建时间
 * @property int $update_time 修改时间
 */
class NodeExtend extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%node_extend}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type_id', 'status', 'create_time', 'update_time'], 'integer'],
            [['quota'], 'number'],
            [['name', 'type_name', 'company'], 'string', 'max' => 255],
            [['mobile'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', '节点名称'),
            'type_id' => Yii::t('app', '节点类型ID'),
            'type_name' => Yii::t('app', '节点类型名称'),
            'quota' => Yii::t('app', '节点配额'),
            'mobile' => Yii::t('app', '节点手机号'),
            'status' => Yii::t('app', '节点状态'),
            'company' => Yii::t('app', '所属公司'),
            'create_time' => Yii::t('app', '创建时间'),
            'update_time' => Yii::t('app', '修改时间'),
        ];
    }
}
