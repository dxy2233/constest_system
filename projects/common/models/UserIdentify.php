<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%user_identify}}".
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property string $realname 真实名
 * @property int $type 认证类型，1 身份证
 * @property string $number 证件号码
 * @property string $pic_front 证件图片前面
 * @property string $pic_back 证件图片后面
 * @property int $status 状态，0 待审核，1 审核成功，2 审核失败
 * @property string $status_remark 状态备注
 * @property int $audit_admin_id 审核人ID
 * @property int $audit_time 审核时间
 * @property int $create_time 添加时间
 * @property int $update_time 修改时间
 */
class UserIdentify extends \common\dzbase\DzModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_identify}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'status', 'audit_admin_id', 'audit_time', 'create_time', 'update_time'], 'integer'],
            [['realname'], 'string', 'max' => 20],
            [['number', 'status_remark'], 'string', 'max' => 50],
            [['pic_front', 'pic_back'], 'string', 'max' => 100],
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
            'realname' => Yii::t('app', '真实名'),
            'type' => Yii::t('app', '认证类型，1 身份证'),
            'number' => Yii::t('app', '证件号码'),
            'pic_front' => Yii::t('app', '证件图片前面'),
            'pic_back' => Yii::t('app', '证件图片后面'),
            'status' => Yii::t('app', '状态，0 待审核，1 审核成功，2 审核失败'),
            'status_remark' => Yii::t('app', '状态备注'),
            'audit_admin_id' => Yii::t('app', '审核人ID'),
            'audit_time' => Yii::t('app', '审核时间'),
            'create_time' => Yii::t('app', '添加时间'),
            'update_time' => Yii::t('app', '修改时间'),
        ];
    }
}
