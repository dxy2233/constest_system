<?php

namespace common\models\business;

use yii\behaviors\TimestampBehavior;
use common\models\business\BUserOther;

class BUser extends \common\models\User
{
    public static $STATUS_ON = 1;
    public static $STATUS_OFF = 0;

    public static function getStatus($key = '')
    {
        $arr = [
            self::$STATUS_ON => \Yii::t('app', '正常'),
            self::$STATUS_OFF => \Yii::t('app', '冻结'),
        ];
        if ($key !== "") {
            return isset($arr[$key]) ? $arr[$key] : "";
        }

        return $arr;
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
     * 用户实名认证
     *  一对一
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
     * 用户推荐人
     *  一对多
     * @return void
     */
    public function getUserRecommend()
    {
        return $this->hasMany(BUserRecommend::className(), ['parent_id' => 'id']);
    }
    /**
     * 用户的投票劵
     *
     * @return void
     */
    public function getVouchers()
    {
        return $this->hasMany(BVoucher::className(), ['user_id' => 'id']);
    }

    /**
     * 用户的投票劵资产
     *
     * @return void
     */
    public function getUserVoucher()
    {
        return $this->hasOne(BUserVoucher::className(), ['user_id' => 'id']);
    }

    /**
     * 用户节点
     *
     * @return void
     */
    public function getNode()
    {
        return $this->hasOne(BNode::className(), ['user_id' => 'id'])->where(['<>', 'status', BNode::STATUS_DEL]);
    }
    
    /**
     * 用户的投票劵使用记录
     *
     * @return void
     */
    public function getVoucherDetails()
    {
        return $this->hasMany(BVoucherDetail::className(), ['user_id' => 'id']);
    }

    /**
     * 用户的投票劵使用记录
     *
     * @return void
     */
    public function getUserRechargeAddress()
    {
        return $this->hasOne(BUserRechargeAddress::className(), ['user_id' => 'id']);
    }

    /**
     * 用户的投票记录
     *
     * @return void
     */
    public function getVotes()
    {
        return $this->hasMany(BVote::className(), ['user_id' => 'id']);
    }
    /**
     * 用户的其他信息
     *
     * @return void
     */
    public function getUserOther()
    {
        return $this->hasOne(BUserOther::className(), ['user_id' => 'id']);
    }
}
