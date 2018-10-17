<?php

namespace common\models\business;

class BVote extends \common\models\Vote
{
    const TYPE_ORDINARY = 1; // 普通投票
    const TYPE_PAY = 2; // 支付投票
    const TYPE_VOUCHER = 3; // 券投票
    const TYPE_RECOMMEND = 4; // 推荐投票

    public static function getType($key = '')
    {
        $arr = [
            static::TYPE_ORDINARY => \Yii::t('app', '普通投票'),
            static::TYPE_PAY => \Yii::t('app', '支付投票'),
            static::TYPE_VOUCHER => \Yii::t('app', '券投票'),
            static::TYPE_RECOMMEND => \Yii::t('app', '推荐投票'),
        ];
        if ($key !== '') {
            return isset($arr[$key]) ? $arr[$key] : '';
        }

        return $arr;
    }

    public static function getStatus($key = '')
    {
        $arr = [
            static::STATUS_ACTIVE => \Yii::t('app', '投出'),
            static::STATUS_INACTIVE => \Yii::t('app', '撤回'),
        ];
        if ($key !== '') {
            return isset($arr[$key]) ? $arr[$key] : '';
        }

        return $arr;
    }

    /**
     * 用户币种
     *  一对多
     * @return void
     */
    public function getUser()
    {
        return $this->hasOne(BUser::className(), ['id' => 'user_id']);
    }
    /**
    * 自定义 label
    * @return array
    */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [

        ]);
    }
}
