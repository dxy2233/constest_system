<?php
namespace admin\services;

use common\models\AdminUser;
use common\models\business\BAdminUser;
use common\models\business\BAdminLog;

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
        } else {
            self::writeUserLog($user->id, BAdminLog::$TYPE_LOGIN, BAdminLog::$STATUS_FAIL, '账号或者密码错误', \Yii::$app->request->getUserIP());
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

    public static function writeUserLog($userId, $type, $status, $content = '', $ip = '')
    {
        $userLog = new BAdminLog();
        $userLog->user_id = $userId;
        $userLog->type = $type;
        $userLog->content = $content;
        $userLog->status = $status;
        $userLog->ip = $ip;
        $userLog->create_time = time();

        $sign = $userLog->save();
        if (!$sign) {
            var_dump($userId);
            var_dump($type);
            var_dump($status);
            var_dump($content);
            var_dump($ip);
            var_dump($userLog->create_time);
            exit;
        }
    }
}
