<?php

namespace common\models\business;

use yii\behaviors\TimestampBehavior;

class BUser extends \common\models\User
{
    public function behaviors()
    {
        return [
            [
                // 自动添加时间
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    # 创建之前
                    self::EVENT_BEFORE_INSERT => ['create_time', 'update_time'],
                    # 修改之前
                    self::EVENT_BEFORE_UPDATE => ['update_time']
                ],
                #设置默认值
                'value' => NOW_TIME
            ]
        ];
    }
    // 需要事务的操作
    public function transactions()
    {
        return [
            // 'admin' => self::OP_INSERT,
            // 'api' => self::OP_INSERT | self::OP_UPDATE | self::OP_DELETE,
            // 上面等价于：
            // 'api' => self::OP_ALL,
            'app' => self::OP_INSERT,
        ];
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
    /**
     * 用户钱包
     *  一对多
     * @return void
     */
    public function getIdentify()
    {
        return $this->hasOne(BUserIdentify::className(), ['user_id' => 'id']);
    }
    /**
     * 用户钱包
     *  一对多
     * @return void
     */
    public function getUserWallet()
    {
        return $this->hasMany(BUserWallet::className(), ['user_id' => 'id']);
    }
    /**
     * 用户币种
     *  一对多
     * @return void
     */
    public function getUserCurrency()
    {
        return $this->hasMany(BUserCurrency::className(), ['user_id' => 'id']);
    }
    /**
     * 用户冻结明细
     *  一对多
     * @return void
     */
    public function getUserCurrencyFrozen()
    {
        return $this->hasMany(BUserCurrencyFrozen::className(), ['user_id' => 'id']);
    }
    /**
     * 用户推荐
     *  一对多
     * @return void
     */
    public function getUserRecommend()
    {
        return $this->hasOne(BUserRecommend::className(), ['user_id' => 'id']);
    }
}
