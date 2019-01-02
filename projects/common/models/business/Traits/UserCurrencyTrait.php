<?php
namespace common\models\business\Traits;

trait UserCurrencyTrait
{
    // 充值
    public static $TYPE_RECHARGE = 1;
    // 提现
    public static $TYPE_WITHDRAW = 2;
    // 选举
    public static $TYPE_ELECTION = 3;
    // 投票
    public static $TYPE_VOTE = 4;
    // 手续费
    public static $TYPE_POUNDAGE = 5;
    // 奖励
    public static $TYPE_REWARD = 6;

    // 弃用
    // 赎回选举
    // public static $TYPE_ELECTION_REDEEM = 6;
    // 赎回投票
    // public static $TYPE_VOTE_REDEEM = 7;

    public static function getType(int $key = null)
    {
        $arr = [
            static::$TYPE_RECHARGE => \Yii::t('app', '转入'),
            static::$TYPE_WITHDRAW => \Yii::t('app', '提现'),
            static::$TYPE_ELECTION => \Yii::t('app', '选举'),
            static::$TYPE_VOTE => \Yii::t('app', '投票'),
            static::$TYPE_POUNDAGE => \Yii::t('app', '手续费'),
            static::$TYPE_REWARD => \Yii::t('app', '奖励'),
            // static::$TYPE_ELECTION_REDEEM => \Yii::t('app', '赎回选举'),
            // static::$TYPE_VOTE_REDEEM => \Yii::t('app', '赎回投票'),
        ];
        if (!is_null($key)) {
            return isset($arr[$key]) ? $arr[$key] : null;
        }

        return $arr;
    }
    
    /** 明细表  BUserCurrencyDetail
     * 获取所有收入 类型
     *
     * @return void
     */
    public static function getTypeRevenue(int $key = null)
    {
        $arr = [
            static::$TYPE_RECHARGE,
            static::$TYPE_REWARD,
        ];
        if (!is_null($key)) {
            return in_array($key, $arr) ? true : false;
        }
        return $arr;
    }
    /** 明细表  BUserCurrencyDetail
     * 获取所有支出 类型
     *
     * @return void
     */
    public static function getTypePay(int $key = null)
    {
        $arr = [
            static::$TYPE_WITHDRAW,
            static::$TYPE_VOTE,
            static::$TYPE_POUNDAGE,
        ];
        if (!is_null($key)) {
            return in_array($key, $arr) ? true : false;
        }
        return $arr;
    }
    /** 冻结表  BUserCurrencyFrozen
     * 获取锁仓 类型
     *
     * @param integer $key
     * @return void
     */
    public static function getTypeFrozen(int $key = null)
    {
        $arr = [
            static::$TYPE_ELECTION,
            static::$TYPE_VOTE,
            static::$TYPE_WITHDRAW,
        ];
        if (!is_null($key)) {
            return in_array($key, $arr) ? true : false;
        }
        return $arr;
    }
}
