<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%node_type}}".
 *
 * @property int $id
 * @property string $name 节点名
 * @property string $min_money 最低标准
 * @property int $max_people 人数上限
 * @property int $max_candidate 候选人上限
 * @property int $is_examine 是否审核
 * @property int $is_candidate 是否开启候选功能
 * @property int $is_vote 是否开启投票功能
 * @property int $is_order 是否开启排名功能
 * @property int $tenure_num 任职数量
 * @property int $status 启用状态
 * @property int $sort 排序
 * @property int $create_time
 */
class NodeType extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%node_type}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name',  'max_candidate', 'is_examine', 'is_candidate', 'is_vote', 'is_order', 'tenure_num', 'status', 'create_time'], 'required'],
            [[ 'max_candidate', 'is_examine', 'is_candidate', 'is_vote', 'is_order', 'tenure_num', 'status', 'sort', 'create_time'], 'integer'],
            [['name'], 'string', 'max' => 16],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '节点名',
            'conversion' => '三通折合grt数量',
            'grt' => 'grt最低标准',
            'tt' => 'tt最低标准',
            'bpt' => 'bpt最低标准',
            'gdt_reward' => '奖励gdt数量',
            'max_candidate' => '候选人上限',
            'quota' => '销售配额',
            'is_examine' => '是否审核',
            'is_candidate' => '是否开启候选功能',
            'is_vote' => '是否开启投票功能',
            'is_order' => '是否开启排名功能',
            'tenure_num' => '任职数量',
            'status' => '启用状态',
            'sort' => '排序',
            'create_time' => 'Create Time',
        ];
    }
}
