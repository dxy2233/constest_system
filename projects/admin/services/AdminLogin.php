<?php
namespace admin\services;

use common\models\AdminUser;

/**
 * Created by dazhengtech.com
 * User: Dazhengtech.com
 * Date: 16/7/2
 * Time: 下午1:07
 */
class AdminLogin
{

    /**
     * 管理员登录验证
     * @param $userName
     * @param $password
     * @return bool
     */
    public static function login($userName, $password)
    {
        $user = AdminUser::findOne(['name' => $userName]);
        if ($user == null) {
            return false;
        }

        if (md5($password . $user->pwd_salt) == $user->password) {
            $user->last_login_time = time();
            return $user;
        }

        return false;
    }

    /**
     * 管理员注销登录
     */
    public static function logout()
    {
        \Yii::$app->user->logout();
    }
}
