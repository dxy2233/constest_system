<?php

namespace common\services;

use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use common\components\NetUtil;
use common\services\ReturnInfo;
use common\components\FuncHelper;
use common\components\FuncResult;
use common\models\business\BUser;
use common\models\business\BUserLog;
use common\models\business\BUserWallet;
use common\models\business\BUserAccessToken;
use common\models\business\BUserRefreshToken;

class UserService extends ServiceBase
{
    /**
     * 用户登录
     *
     * @param BUser $user
     * @return void
     */
    public static function login(BUser $user)
    {
        if ($user->status == BUser::STATUS_INACTIVE) {
            return new ReturnInfo(1, "账号状态异常");
        }

        $user->last_login_ip = NetUtil::getIp();
        $user->last_login_time = NOW_TIME;
        $user->update();
        $accessToken = self::setAccessToken($user->id);
        if ($accessToken->code != 0) {
            return new ReturnInfo(1, $accessToken->msg, $accessToken->content);
        }
        // 写日志
        self::writeUserLog($user->id, BUserLog::$TYPE_LOGIN, BUserLog::$STATUS_SUCCESS, '登录成功', $user->last_login_ip);
        $accessToken->content['name'] = $user->username;
        $accessToken->content['mobile'] = $user->mobile;
        $defaultWallet = FuncHelper::getDefaultWallet();
        $userWallet = $user->getUserWallet()->select(['address', 'wallet'])->where(['wallet' => $defaultWallet['code']])->asArray()->all();
        $accessToken->content['wallet_address'] = $userWallet;
        return new ReturnInfo(0, "登录成功", $accessToken->content);
    }
    
    /**
     * 创建用户
     *
     * @param array $data
     * @return void
     */
    public static function createUser(array $data)
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $userModel = new BUser();
            foreach ($data as $key => $value) {
                if ($userModel->hasAttribute($key)) {
                    $userModel->$key = $value;
                }
            }
            // 注册账号为激活状态
            $userModel->status = Buser::STATUS_ACTIVE;
            if ($userModel->save()) {
                // 保存成功写入钱包地址
                if (isset($data['wallet_address'])) {
                    $userWallet = new BUserWallet();
                    $defaultWallet = FuncHelper::getDefaultWallet();
                    $userWallet->wallet = isset($data['wallet_code']) ? $data['wallet_code'] : $defaultWallet['code'];
                    $userWallet->address = $data['wallet_address'];
                    $userWallet->link('user', $userModel);
                    // 数据更新失败
                    if (empty($userWallet->id)) {
                        throw new ErrorException($userWallet->getFirstError());
                    }
                }
            } else {
                throw new ErrorException($userModel->getFirstError());
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return new ReturnInfo(1, "创建失败", $e->getMessage());
        }
        self::writeUserLog($userModel->id, BUserLog::$TYPE_REGISTER, BUserLog::$STATUS_SUCCESS, '创建成功', NetUtil::getIp());
        return new ReturnInfo(0, "创建成功", $userModel);
    }


    /**
     * 生成用户accessToken
     */
    public static function setAccessToken($userId)
    {

        //过期旧token，保持一个用户登录
        if (!empty(\Yii::$app->params['onlyUserlogin'])) {
            BUserAccessToken::updateAll(
                ['expire_time' => NOW_TIME, 'update_time' => NOW_TIME],
                ['and', ['user_id' => $userId], ['client_id' => \Yii::$app->controller->module->id], ['>', 'expire_time', NOW_TIME]]
            );
            BUserRefreshToken::updateAll(
                ['expire_time' => NOW_TIME, 'update_time' => NOW_TIME],
                ['and', ['user_id' => $userId], ['client_id' => \Yii::$app->controller->module->id], ['>', 'expire_time', NOW_TIME]]
            );
        }

        //添加新token
        $accessToken = new BUserAccessToken();
        $accessToken->client_id = \Yii::$app->controller->module->id;
        $accessToken->user_id = $userId;
        $accessToken->access_token = $accessToken->generateAccessToken();
        $accessToken->expire_time = NOW_TIME + 3600 * 24;
        $accessToken->create_time = NOW_TIME;
        $accessToken->update_time = NOW_TIME;
        $accessToken->insert();

        $refreshToken = new BUserRefreshToken();
        $refreshToken->client_id = \Yii::$app->controller->module->id;
        $refreshToken->user_id = $userId;
        $refreshToken->refresh_token = $refreshToken->generateRefreshToken();
        $refreshToken->expire_time = NOW_TIME + 3600 * 24;
        $refreshToken->create_time = NOW_TIME;
        $refreshToken->update_time = NOW_TIME;
        $refreshToken->insert();

        return new ReturnInfo(0, "生成用户认证成功", [
            'access_token' => $accessToken->access_token,
            'expire_time' => $accessToken->expire_time,
            'refresh_token' => $refreshToken->refresh_token,
        ]);
    }

    /**
     * 刷新用户accessToken
     */
    public static function refreshAccessToken($refreshToken)
    {
        $refreshToken = BUserRefreshToken::find()
            ->where(['refresh_token' => $refreshToken,'client_id' => \Yii::$app->controller->module->id])
            ->andWhere(['>=', 'expire_time', NOW_TIME])
            ->one();

        if ($refreshToken) {
            $accessToken = new BUserAccessToken();
            $accessToken->client_id = $refreshToken->client_id;
            $accessToken->user_id = $refreshToken->user_id;
            $accessToken->access_token = $accessToken->generateAccessToken();
            $accessToken->expire_time = NOW_TIME + 3600 * 24;
            $accessToken->create_time = NOW_TIME;
            $accessToken->update_time = NOW_TIME;
            $accessToken->insert();

            $refreshToken->refresh_token = $refreshToken->generateRefreshToken();
            $refreshToken->expire_time = NOW_TIME + 3600 * 24;
            $refreshToken->update_time = NOW_TIME;
            $refreshToken->update();

            return new ReturnInfo(0, "刷新用户认证成功", [
                'access_token' => $accessToken->access_token,
                'expire_time' => $accessToken->expire_time,
                'refresh_token' => $refreshToken->refresh_token,
            ]);
        }

        return new ReturnInfo(1, "刷新用户认证失败");
    }
    /**
     * @param $userId
     * @param $type
     * @param $status
     * @param string $content
     * @param string $ip
     * info: 写用户日志
     */
    public static function writeUserLog($userId, $type, $status, $content = '', $ip = '')
    {
        $userLog = new BUserLog();
        $userLog->user_id = $userId;
        $userLog->type = $type;
        $userLog->content = $content;
        $userLog->status = $status;
        $userLog->client_id = \Yii::$app->controller->module->id;
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
    /**
     * 用户注销登录
     */
    public static function logout()
    {
       return \Yii::$app->user->logout();
    }
}
