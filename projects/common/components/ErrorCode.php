<?php
namespace common\components;
/**
 * Created by dazhengtech.com
 * User: Dazhengtech.com
 * Date: 16/1/23
 * Time: 下午5:25
 */

class ErrorCode {

    public static $NONE = [0, ''];
    public static $OTHERS = [9000, '未知错误'];

    public static $BID_PROJECT_NOT_BIDDABLE = [1001, '项目现在处于不可投标状态'];
    public static $BID_PROJECT_HAS_MIN_INVEST_AMOUNT = [1002, '达不到项目最小投资额'];
    public static $BID_PROJECT_EXCEED_AMOUNT = [1003, '超过了项目的可投额度'];
    public static $BID_PROJECT_FULL_AMOUNT = [1004, '项目已满标'];



    public static $PROJECT_NOT_EXIST = [3001, '项目不存在'];
    public static $PROJECT_NOT_ON_RAISE_END = [3002, '项目募集未满'];
    public static $PROJECT_REPAY_PLAN_NOT_EXIST = [3003, '项目还款计划不存在'];

    public static $USER_BALANCE_OUT = [2001, '用户余额不足'];
}