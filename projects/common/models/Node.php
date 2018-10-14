<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%node}}".
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property int $type_id 节点类型ID
 * @property string $name 机构/名称
 * @property string $desc 简介
 * @property string $scheme 建设方案
 * @property int $status 节点状态：-1 后台停用 0 审核中 1 已生效 2 撤销 
 * @property string $status_remark 状态备注
 * @property int $create_time 创建时间
 */
class Node extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%node}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'type_id', 'name', 'desc', 'scheme', 'status', 'create_time'], 'required'],
            [['user_id', 'type_id', 'status', 'create_time'], 'integer'],
            [['desc', 'scheme'], 'string'],
            [['name', 'status_remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'type_id' => '节点类型ID',
            'name' => '机构/名称',
            'desc' => '简介',
            'scheme' => '建设方案',
            'status' => '节点状态：-1 后台停用 0 审核中 1 已生效 2 撤销 ',
            'status_remark' => '状态备注',
            'create_time' => '创建时间',
        ];
    }
}
