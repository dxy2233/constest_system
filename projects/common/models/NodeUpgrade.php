<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%node_upgrade}}".
 *
 * @property int $id
 * @property int $node_id 节点ID
 * @property string $name 节点名称
 * @property string $logo LOGO
 * @property string $desc 简介
 * @property string $scheme 建设方案
 * @property string $weixin 微信号
 * @property int $old_type 前类型
 * @property int $type_id 类型
 * @property int $parent_id 补录推荐人ID
 * @property int $status 状态 0 待审核 1 已审核 2 审核失败,
 * @property string $status_remark 状态备注
 * @property int $grt 质押grt
 * @property string $grt_address 贵人通地址
 * @property int $tt 质押tt
 * @property string $tt_address 茶通地址
 * @property int $bpt 质押bpt
 * @property string $bpt_address 美食通地址
 * @property int $examine_time 审核时间
 * @property int $create_time 创建时间
 * @property int $update_time 修改时间
 */
class NodeUpgrade extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%node_upgrade}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'old_type', 'type_id', 'parent_id', 'status', 'grt', 'tt', 'bpt', 'examine_time', 'create_time', 'update_time'], 'integer'],
            [['user_id', 'type_id'], 'required'],
            [['desc', 'scheme'], 'string'],
            [['name', 'logo', 'grt_address', 'tt_address', 'bpt_address'], 'string', 'max' => 256],
            [['weixin'], 'string', 'max' => 128],
            [['status_remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '节点ID',
            'name' => '节点名称',
            'logo' => 'LOGO',
            'desc' => '简介',
            'scheme' => '建设方案',
            'weixin' => '微信号',
            'old_type' => '以前节点类型',
            'type_id' => '节点类型',
            'parent_id' => '补录推荐人ID',
            'status' => '状态 0 待审核 1 已审核 2 审核失败',
            'status_remark' => '状态备注',
            'grt' => '质押grt',
            'grt_address' => '贵人通地址',
            'tt' => '质押tt',
            'tt_address' => '茶通地址',
            'bpt' => '质押bpt',
            'bpt_address' => '美食通地址',
            'old_grt' => '升级前grt数量',
            'old_ttt' => '升级前tt数量',
            'old_bpt' => '升级前bpt数量',
            'examine_time' => '审核时间',
            'create_time' => '创建时间',
            'update_time' => '修改时间',
        ];
    }
}
