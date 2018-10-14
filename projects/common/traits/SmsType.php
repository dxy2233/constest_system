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
    // 团队领队登录
    public static $TYPE_TEAM_LOGIN = 1;

    //用户手机签到
    public static $TYPE_USER_SIGN = 2;

    //用户登录
    public static $TYPE_USER_LOGIN = 3;

    //用户注册
    public static $TYPE_USER_REGISTER = 3;
}
