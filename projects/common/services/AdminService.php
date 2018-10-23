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
use common\models\business\BAdminUser;
use common\models\business\BUserLog;
use common\models\business\BAdminLog;
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

    public static function loginErrorNumLimit($mobile)
    {
        if (empty($mobile)) {
            return new ReturnInfo(1, '账号不存在');
        }
        /*封ip*/
        $lastUserLogArr = BAdminLog::find()->where(['ip' => \Yii::$app->request->getUserIP(), 'type' => BAdminLog::$TYPE_LOGIN, 'status' => BAdminLog::$STATUS_FAIL])
            ->select('create_time')->asArray()->orderBy('create_time desc')->limit(1)->one();
        if (!empty($lastUserLogArr) && NOW_TIME - $lastUserLogArr['create_time'] < 3600) {
            $userLogCount = BAdminLog::find()->where(['ip' => \Yii::$app->request->getUserIP(), 'type' => BAdminLog::$TYPE_LOGIN, 'status' => BAdminLog::$STATUS_FAIL])
                ->andWhere(['<=', 'create_time', $lastUserLogArr['create_time']])
                ->andWhere(['>', 'create_time', $lastUserLogArr['create_time'] - 3600])->count();
            if ($userLogCount >= 5) {
                return new ReturnInfo(1, '您已输入密码错误5次，账户将冻结1小时');
            }
        }

        /*封账号*/
        // 根据用户信息找到现在到过去2小时登录信息
        $user = BAdminUser::find()->where(['name' => $mobile])->limit(1)->one();

        $count = 0;
        if (!empty($user)) {
            $lastUserLogArr = BAdminLog::find()->where(['user_id' => $user->id, 'type' => BAdminLog::$TYPE_LOGIN, 'status' => BAdminLog::$STATUS_FAIL])
                ->asArray()->orderBy('create_time desc')->limit(1)->one();

            if (!empty($lastUserLogArr) && NOW_TIME - $lastUserLogArr['create_time'] < 3600) {
                $count = BAdminLog::find()->where(['user_id' => $user->id, 'type' => BAdminLog::$TYPE_LOGIN, 'status' => BAdminLog::$STATUS_FAIL])
                    ->asArray()->andWhere(['<=', 'create_time', $lastUserLogArr['create_time']])
                    ->andWhere(['>', 'create_time', $lastUserLogArr['create_time'] - 3600])->count();
                if ($count >= 5) {
                    return new ReturnInfo(1, '您已输入密码错误5次，账户将冻结1小时');
                }
            }
        }

        return new ReturnInfo(0, '', ['count' => $count, 'user_id' => empty($user->id) ? 0 : $user->id]);
    }

    /**
     * 用户注销登录
     */
    public static function logout()
    {
        return \Yii::$app->user->logout();
    }
}
