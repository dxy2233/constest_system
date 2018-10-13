<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%setting}}".
 *
 * @property int $id
 * @property string $group 设置组
 * @property string $name 设置名称
 * @property string $key 设置键值
 * @property string $value 设置值
 * @property string $default 默认值
 * @property string $type 设置类型：input, redis, select, 等
 * @property string $initialize 初始化数据
 * @property string $remark 备注提示
 * @property int $create_time 创建时间
 * @property int $update_time 修改时间
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
            [['name', 'key', 'initialize', 'remark', 'create_time', 'update_time'], 'required'],
            [['initialize'], 'string'],
            [['create_time', 'update_time'], 'integer'],
            [['group', 'key', 'type'], 'string', 'max' => 50],
            [['name'], 'string', 'max' => 100],
            [['value', 'default', 'remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'group' => Yii::t('app', '设置组'),
            'name' => Yii::t('app', '设置名称'),
            'key' => Yii::t('app', '设置键值'),
            'value' => Yii::t('app', '设置值'),
            'default' => Yii::t('app', '默认值'),
            'type' => Yii::t('app', '设置类型：input, redis, select, 等'),
            'initialize' => Yii::t('app', '初始化数据'),
            'remark' => Yii::t('app', '备注提示'),
            'create_time' => Yii::t('app', '创建时间'),
            'update_time' => Yii::t('app', '修改时间'),
        ];
    }
}
