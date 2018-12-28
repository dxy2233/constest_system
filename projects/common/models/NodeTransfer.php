<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%transfer}}".
 *
 * @property int $id
 * @property int $from_user_id 转出用户
 * @property int $to_user_id 转入用户
 * @property int $node_id 节点ID
 * @property string $images 申请凭证
 * @property int $status 审核状态 0
 * @property string $status_remark 状态备注
 * @property int $create_time 创建时间
 * @property int $examine_time 审核时间
 * @property int $examine_user 审核管理员
 * @property int $update_time 修改时间
 */
class NodeTransfer extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%node_transfer}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'from_user_id', 'to_user_id', 'node_id', 'status', 'create_time', 'examine_time', 'examine_user', 'update_time'], 'integer'],
            [['images'], 'string', 'max' => 1000],
            [['status_remark'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'from_user_id' => '转出用户',
            'to_user_id' => '转入用户',
            'node_id' => '节点ID',
            'images' => '申请凭证',
            'status' => '审核状态 0',
            'grt' => '转让时grt数量',
            'tt' => '转让时tt数量',
            'bpt' => '转让时bpt数量',
            'status_remark' => '状态备注',
            'create_time' => '创建时间',
            'examine_time' => '审核时间',
            'examine_user' => '审核管理员',
            'update_time' => '修改时间',
        ];
    }
}
