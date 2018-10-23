<?php

namespace common\services;

use common\models\business\BUserCurrency;
use common\models\business\BUserCurrencyDetail;
use common\models\business\BUserCurrencyFrozen;
use common\models\User;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use common\components\NetUtil;
use common\services\ReturnInfo;
use common\components\FuncHelper;
use common\components\FuncResult;
use common\models\business\BUser;
use common\models\business\BUserLog;
use common\models\business\BUserWallet;
use common\models\business\BAdminAccessToken;
use common\models\business\BVoucherDetail;
use common\models\business\BUserVoucher;
use common\models\business\BVoucher;
use common\models\business\BAdminRefreshToken;

class AdminService extends ServiceBase
{
    


    /**
     * 生成用户accessToken
     */
    public static function setAccessToken($userId)
    {

        //过期旧token，保持一个用户登录
        if (!empty(\Yii::$app->params['onlyUserlogin'])) {
            BAdminAccessToken::updateAll(
                ['expire_time' => NOW_TIME, 'update_time' => NOW_TIME],
                ['and', ['user_id' => $userId], ['client_id' => \Yii::$app->controller->module->id], ['>', 'expire_time', NOW_TIME]]
            );
            BAdminRefreshToken::updateAll(
                ['expire_time' => NOW_TIME, 'update_time' => NOW_TIME],
                ['and', ['user_id' => $userId], ['client_id' => \Yii::$app->controller->module->id], ['>', 'expire_time', NOW_TIME]]
            );
        }

        //添加新token
        $accessToken = new BAdminAccessToken();
        $accessToken->client_id = \Yii::$app->controller->module->id;
        $accessToken->user_id = $userId;
        $accessToken->access_token = $accessToken->generateAccessToken();
        $accessToken->expire_time = NOW_TIME + 3600 * 24;
        $accessToken->create_time = NOW_TIME;
        $accessToken->update_time = NOW_TIME;
        $accessToken->insert();

        $refreshToken = new BAdminRefreshToken();
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
        $refreshToken = BAdminRefreshToken::find()
            ->where(['refresh_token' => $refreshToken,'client_id' => \Yii::$app->controller->module->id])
            ->andWhere(['>=', 'expire_time', NOW_TIME])
            ->one();

        if ($refreshToken) {
            $accessToken = new BAdminAccessToken();
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
     * 用户注销登录
     */
    public static function logout()
    {
        return \Yii::$app->user->logout();
    }
}
