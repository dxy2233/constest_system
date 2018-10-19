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
 * @property int $status 节点状态：0 停用 1 已生效 2 审核 3 撤销
 * @property string $status_remark 状态备注
 * @property string $logo LOGO
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
            [['user_id', 'type_id'], 'required'],
            [['user_id', 'type_id', 'status', 'create_time', 'is_tenure'], 'integer'],
            [['desc', 'scheme'], 'string'],
            [['name', 'status_remark', 'logo'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', '用户ID'),
            'type_id' => Yii::t('app', '节点类型ID'),
            'name' => Yii::t('app', '机构/名称'),
            'grt' => Yii::t('app', '质压grt'),
            'tt' => Yii::t('app', '质压tt'),
            'bpt' => Yii::t('app', '质压bpt'),
            'desc' => Yii::t('app', '简介'),
            'scheme' => Yii::t('app', '建设方案'),
            'is_tenure' => Yii::t('app', '任职状态：0 不任职 1 任职 '),
            'status' => Yii::t('app', '节点状态：0 停用 1 已生效 2 审核 3 撤销 '),
            'status_remark' => Yii::t('app', '状态备注'),
            'logo' => Yii::t('app', 'LOGO'),
            'create_time' => Yii::t('app', '创建时间'),
        ];
    }
}