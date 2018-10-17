<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%sms_auth}}".
 *
 * @property integer $id
 * @property string $mobile
 * @property string $content
 * @property integer $user_id
 * @property integer $type
 * @property integer $status
 * @property integer $create_time
 */
class SmsAuth extends \common\dzbase\DzModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sms_auth}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile', 'content', 'type'], 'required'],
            [['user_id', 'type', 'status', 'create_time'], 'integer'],
            [['mobile'], 'string', 'max' => 15],
            [['content'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mobile' => '手机号码',
            'content' => '验证码',
            'user_id' => '用户ID',
            'type' => '验证类型',
            'status' => '是否验证或者使用，0否，1是',
            'create_time' => '添加时间',
        ];
    }
}
