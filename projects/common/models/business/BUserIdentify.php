<?php

namespace common\models\business;

use common\components\FuncHelper;

class BUserIdentify extends \common\models\UserIdentify
{
    const STATUS_INACTIVE = 0; //待审核
    const STATUS_ACTIVE = 1; //正常
    const STATUS_FAIL = 2; //认证失败

    public static function getStatus($key = '')
    {
        $arr = [
            static::STATUS_ACTIVE => \Yii::t('app', '审核成功'),
            static::STATUS_INACTIVE => \Yii::t('app', '待审核'),
            static::STATUS_FAIL => \Yii::t('app', '认证失败'),
        ];
        if ($key !== '') {
            return isset($arr[$key]) ? $arr[$key] : '';
        }

        return $arr;
    }

    /**
     * 转换pic_front链接
     *
     * @return void
     */
    public function getPicFrontText()
    {
        return FuncHelper::getImageUrl($this->pic_front, 640, 640);
    }
    /**
     * 转换pic_back链接
     *
     * @return void
     */
    public function getPicBackText()
    {
        return FuncHelper::getImageUrl($this->pic_back, 640, 640);
    }

    /**
     * 认证用户
     *  一对多
     * @return void
     */
    public function getUser()
    {
        return $this->hasOne(BUser::className(), ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['realname', 'number', 'pic_front', 'pic_back'], 'required'],
            // [['number'], 'unique'],
            [['number'], 'match', 'pattern' => '/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/'],
            // 身份证排重
            [['number'], 'unique', 'filter' => function ($query) {
                $query->where(['number' => $this->number]);
                $query->andWhere(['<>', 'status', self::STATUS_FAIL]);
            }, 'message' => '此{attribute}已提交实名认证'],
            [['status'], 'default', 'value' => static::STATUS_INACTIVE + 10],
        ]);
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
