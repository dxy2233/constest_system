<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%notice}}".
 *
 * @property int $id
 * @property string $title 标题
 * @property string $desc 描述
 * @property string $detail 详情
 * @property int $type 公告类型：0：链接 1：正文（文章类）
 * @property string $url 链接地址
 * @property int $sort 排序
 * @property int $status 状态：0 关闭 1 启用
 * @property int $start_time 开始时间
 * @property int $end_time 结束时间
 * @property int $click 点击量
 * @property int $create_time 创建时间
 * @property int $update_time 修改时间
 */
class Notice extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%notice}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'type'], 'required'],
            [['detail'], 'string'],
            [['type', 'sort', 'status',  'click', 'create_time', 'update_time'], 'integer'],
            [['title', 'desc', 'url', 'image'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', '标题'),
            'desc' => Yii::t('app', '描述'),
            'detail' => Yii::t('app', '详情'),
            'type' => Yii::t('app', '公告类型：0：链接 1：正文（文章类）'),
            'url' => Yii::t('app', '链接地址'),
            'image' => Yii::t('app', '展示图'),
            'sort' => Yii::t('app', '排序'),
            'is_top' => Yii::t('app', '是否置顶'),
            'status' => Yii::t('app', '状态：0 关闭 1 启用'),
            'start_time' => Yii::t('app', '开始时间'),
            'end_time' => Yii::t('app', '结束时间'),
            'click' => Yii::t('app', '点击量'),
            'create_time' => Yii::t('app', '创建时间'),
            'update_time' => Yii::t('app', '修改时间'),
        ];
    }
}
