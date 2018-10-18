<?php
/*
 * @Author: jayden
 * @Date: 2018-09-28 17:34:21
 * @Last Modified by: mikey.zhaopeng
 * @Last Modified time: 2018-09-28 17:34:21
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

    //用户转账短信验证码获取
    public static $TYPE_TRANSFER_GET = 4;
}
