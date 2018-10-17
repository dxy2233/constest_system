<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%currency}}".
 *
 * @property int $id 货币ID
 * @property string $code 编码
 * @property string $name 标题
 * @property string $summary 摘要
 * @property int $status 货币状态：0 下架，1 正常
 * @property int $sort 排序号
 * @property int $create_time 添加时间
 * @property int $update_time 修改时间
 */
class Currency extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%currency}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['wallet_id', 'code'], 'required'],
            [['summary'], 'string'],
            [['status', 'sort', 'create_time', 'update_time', 'wallet_id'], 'integer'],
            [['code', 'name'], 'string', 'max' => 20],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', '货币ID'),
            'wallet_id' => Yii::t('app', '钱包ID'),
            'code' => Yii::t('app', '编码'),
            'name' => Yii::t('app', '标题'),
            'summary' => Yii::t('app', '摘要'),
            'status' => Yii::t('app', '货币状态：0 下架，1 正常'),
            'sort' => Yii::t('app', '排序号'),
            'create_time' => Yii::t('app', '添加时间'),
            'update_time' => Yii::t('app', '修改时间'),
        ];
    }
}
