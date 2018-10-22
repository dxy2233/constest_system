<?php

namespace common\models\business;

class BCurrency extends \common\models\Currency
{
    public static $CURRENCY_GRT = 'grt'; // 贵人通
    public static $CURRENCY_BPT = 'bpt'; // 美食通
    public static $CURRENCY_TT = 'tt'; // 茶通

    public static function getJingtumCurrency()
    {
        $arr = [
            self::getCurrencyIdByCode(self::$CURRENCY_GRT),
            self::getCurrencyIdByCode(self::$CURRENCY_BPT),
        ];
        return $arr;
    }

    public static $CURRENCY_STATUS_OFF = 0; // 下架
    public static $CURRENCY_STATUS_ON = 1; // 正常
    public static function getCurrencyStatus($key = '')
    {
        $arr = [
            self::$CURRENCY_STATUS_OFF => \Yii::t('app', '下架'),
            self::$CURRENCY_STATUS_ON => \Yii::t('app', '正常'),
        ];
        if ($key !== '') {
            return isset($arr[$key]) ? $arr[$key] : '';
        }
        return $arr;
    }

    public static $RECHARGE_STATUS_OFF = 0; // 不可充币
    public static $RECHARGE_STATUS_ON = 1; // 可充币
    public static function getRechargeStatus($key = '')
    {
        $arr = [
            self::$RECHARGE_STATUS_OFF => \Yii::t('app', '不可充币'),
            self::$RECHARGE_STATUS_ON => \Yii::t('app', '可充币'),
        ];
        if ($key !== '') {
            return isset($arr[$key]) ? $arr[$key] : '';
        }
        return $arr;
    }

    public static $WITHDRAW_STATUS_OFF = 0; // 不可提币
    public static $WITHDRAW_STATUS_ON = 1; // 可提币
    public static function getWithdrawStatus($key = '')
    {
        $arr = [
            self::$WITHDRAW_STATUS_OFF => \Yii::t('app', '不可提币'),
            self::$WITHDRAW_STATUS_ON => \Yii::t('app', '可提币'),
        ];
        if ($key !== '') {
            return isset($arr[$key]) ? $arr[$key] : '';
        }
        return $arr;
    }

    /**
     * @param $currencyId
     * @return null|static
     * info : 根据id获取货币信息
     */
    public static function getCurrencyInfoById($currencyId)
    {
        if (empty($currencyId)) {
            return null;
        }
        $res = BCurrency::find()->where(['id' => $currencyId])->limit(1)->one();

        return $res;
    }

    /**
     * @param $currencyCode
     * @return null|static
     * info : 根据code获取货币id
     */
    public static function getCurrencyIdByCode($currencyCode)
    {
        if (empty($currencyCode)) {
            return null;
        }
        $res = BCurrency::find()->where(['code' => $currencyCode])->select('id')->limit(1)->one();

        return empty($res) ? null : $res->id;
    }

    /**
     * @return null|static[]
     * info : 获取所有货币信息
     */
    public static function getAllCurrency()
    {
        $res = BCurrency::find()->all();
        if (empty($res)) {
            return null;
        }

        return $res;
    }

    /**
     * @param $currency   object
     * @return array
     * Interface Info : 格式化货币的id和name
     */
    public static function formatCurrencyObject($currency)
    {
        $len = count($currency);
        $str = [];
        for ($i = 0; $i < $len; $i++) {
            $str[ $currency[ $i ]->id ] = $currency[ $i ]->name;
        }
        return $str;
    }

    /**
     * @param $currency   array
     * @return array
     * Interface Info : 格式化標的id和name(已经封装后的)
     */
    public static function formatCurrencyArray($currency)
    {
        $len = count($currency);
        $str = [];
        for ($i = 0; $i < $len; $i++) {
            $str[ $currency[ $i ]['id'] ] = $currency[ $i ]['currency'];
        }
        return $str;
    }

    /**
     * 用户钱包
     *  一对多
     * @return void
     */
    public function getCurrency()
    {
        return $this->hasMany(BUserCurrency::className(), ['currency_id' => 'id']);
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['summary'], 'string'],
            [['status', 'sort', 'recharge_status', 'recharge_amount_precision', 'withdraw_status', 'withdraw_amount_precision', 'recharge_confirmation', 'withdraw_confirmation'], 'integer'],
            [['recharge_min_amount', 'withdraw_min_amount', 'withdraw_max_amount', 'withdraw_audit_amount', 'withdraw_day_amount'], 'number'],
            [['code', 'name'], 'string', 'max' => 20],
            [['status', 'name', 'code', 'recharge_status', 'recharge_amount_precision', 'withdraw_status', 'withdraw_amount_precision',
                'recharge_min_amount', 'withdraw_min_amount', 'sort'], 'required'],
            [['code'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '货币ID',
            'code' => '编码',
            'name' => '标题',
            'summary' => '摘要',
            'status' => '货币状态',
            'sort' => '排序号',
            'recharge_status' => '充值状态',
            'recharge_min_amount' => '充值最小数量',
            'recharge_amount_precision' => '充值数量精度',
            'recharge_confirmation' => '充值确认数',
            'withdraw_confirmation' => '提现确认数',
            'withdraw_status' => '提现状态',
            'withdraw_min_amount' => '提现最小数量',
            'withdraw_amount_precision' => '提现数量精度',
            'create_time' => '添加时间',
            'update_time' => '修改时间',
        ];
    }
}
