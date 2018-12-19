<?php
/*
 * @Author: jayden
 * @Date: 2018-09-28 17:34:21
 * @Last Modified by: mikey.zhaopeng
 * @Last Modified time: 2018-12-19 10:14:51
 */
namespace common\traits;

trait SmsType
{

    //用户登录
    public static $TYPE_USER_LOGIN = 1;

    //用户注册
    public static $TYPE_USER_REGISTER = 2;

    //用户支付密码修改
    public static $TYPE_PAY_PASSWORD = 3;

    //用户转出短信验证码获取
    public static $TYPE_TRANSFER_GET = 4;

    //节点审核通过时通知用户
    public static $TYPE_NODE_EXAMINE = 5;

    // 用户实名认证通知管理员
    public static $TYPE_IDENTIFY_APPLY = 6;

    //节点升级审核通过时通知用户
    public static $TYPE_NODE_UP_EXAMINE = 7;
}
