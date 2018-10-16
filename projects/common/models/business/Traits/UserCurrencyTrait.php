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
    // 赎回选举
    public static $TYPE_ELECTION_REDEEM = 5;
    // 赎回投票
    public static $TYPE_VOTE_REDEEM = 6;

    public static function getType(int $key = null)
    {
        $arr = [
            static::$TYPE_RECHARGE => \Yii::t('app', '收款'),
            static::$TYPE_WITHDRAW => \Yii::t('app', '提现'),
            static::$TYPE_ELECTION => \Yii::t('app', '选举'),
            static::$TYPE_VOTE => \Yii::t('app', '投票'),
            static::$TYPE_ELECTION_REDEEM => \Yii::t('app', '赎回选举'),
            static::$TYPE_VOTE_REDEEM => \Yii::t('app', '赎回投票'),
        ];
        if (!is_null($key)) {
            return isset($arr[$key]) ? $arr[$key] : null;
        }

        return $arr;
    }
    
    /**
     * 获取所有收入 类型
     *
     * @return void
     */
    public static function getTypeRevenue(int $key = null)
    {
        $arr = [
            static::$TYPE_RECHARGE,
            static::$TYPE_ELECTION_REDEEM,
            static::$TYPE_VOTE_REDEEM,
        ];
        if (!is_null($key)) {
            return in_array($key, $arr) ? true : false;
        }
        return $arr;
    }
    /**
     * 获取所有支出 类型
     *
     * @return void
     */
    public static function getTypePay(int $key = null)
    {
        $arr = [
            static::$TYPE_WITHDRAW,
            static::$TYPE_ELECTION,
            static::$TYPE_VOTE,
        ];
        if (!is_null($key)) {
            return in_array($key, $arr) ? true : false;
        }
        return $arr;
    }
}
