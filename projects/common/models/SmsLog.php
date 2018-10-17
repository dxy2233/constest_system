<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%sms_log}}".
 *
 * @property integer $id
 * @property string $mobile
 * @property string $content
 * @property string $ret_code
 * @property integer $user_id
 * @property integer $create_time
 * @property integer $send_time
 */
class SmsLog extends \common\dzbase\DzModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sms_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile', 'ret_code'], 'required'],
            [['user_id', 'create_time', 'send_time'], 'integer'],
            [['mobile'], 'string', 'max' => 15],
            [['content'], 'string', 'max' => 300],
            [['ret_code'], 'string', 'max' => 500],
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
            'content' => '短信内容',
            'ret_code' => '发送结果码',
            'user_id' => '用户ID',
            'create_time' => '添加时间',
            'send_time' => '实际发送时间',
        ];
    }
}
