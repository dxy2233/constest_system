<?php

namespace common\models\business;

use common\models\business\BUser;

class BUserRechargeWithdraw extends \common\models\UserRechargeWithdraw
{
    public $startTime;
    public $endTime;

    public static $TYPE_RECHARGE = 1; //转入积分
    public static $TYPE_WITHDRAW = 2; //提币
    public static function getType($key = '')
    {
        $arr = [
            self::$TYPE_RECHARGE => \Yii::t('app', '转入积分'),
            self::$TYPE_WITHDRAW => \Yii::t('app', '转出积分'),
        ];
        if ($key !== '') {
            return isset($arr[$key]) ? $arr[$key] : '';
        }
        return $arr;
    }

    public static $STATUS_EFFECT_WAIT = 0; // 待确认
    public static $STATUS_EFFECT_SUCCESS = 1; // 操作成功
    public static $STATUS_EFFECT_FAIL = 2; // 操作失败
    public static function getStatus($key = '')
    {
        $arr = [
            self::$STATUS_EFFECT_WAIT => \Yii::t('app', '划拨中'),
            self::$STATUS_EFFECT_SUCCESS => \Yii::t('app', '成功'),
            self::$STATUS_EFFECT_FAIL => \Yii::t('app', '失败'),
        ];
        if ($key !== '') {
            return isset($arr[$key]) ? $arr[$key] : '';
        } else {
            return $arr;
        }
    }




    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '积分记录ID',
            'order_number' => '订单号',
            'currency_id' => '积分',
            'user_id' => '用户名',
            'mobile' => '手机号码',
            'type' => '类型',
            'amount' => '数量',
            'poundage' => '手续费',
            'source_address' => '发送方地址',
            'destination_address' => '接收方地址',
            'remark' => '备注',
            'status' => '状态',
            'status_remark' => '状态备注',
            'audit_admin_id' => '操作人',
            'audit_time' => '操作时间',
            'create_time' => '添加时间',
            'update_time' => '修改时间',
        ];
    }

    /**
     * 转化时间格式
     */
    public function getCreateTimeText()
    {
        return date('Y-m-d H:i:s', $this->create_time);
    }
    
    /**
     * 转化时间格式
     */
    public function getUpdateTimeText()
    {
        return date('Y-m-d H:i:s', $this->update_time);
    }

    /** 获取用户
     * 多对一关系
     */
    public function getUser()
    {
        return $this->hasOne(BUser::className(), ['id' => 'user_id']);
    }

    /** 获取用户
     * 多对一关系
     */
    public function getCurrency()
    {
        return $this->hasOne(BCurrency::className(), ['id' => 'currency_id']);
    }
}
