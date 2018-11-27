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
use common\models\business\BUserRecommend;
use common\models\business\BUserAccessToken;
use common\models\business\BVoucherDetail;
use common\models\business\BUserVoucher;
use common\models\business\BVoucher;
use common\models\business\BUserRefreshToken;

class UserService extends ServiceBase
{
    /**
     * 生成用户推荐码
     *
     * @param integer $len
     * @return void
     */
    public static function generateRemmendCode(int $len = 6)
    {
        $code = FuncHelper::random($len);
        if (BUser::find()->where(['recommend_code' => $code])->exists()) {
            return self::generateRemmendCode($len);
        }
        return $code;
    }

    /**
     * 验证用户推荐码
     *
     * @param integer $len
     * @return void
     */
    public static function validateRemmendCode(string $code)
    {
        $userModel = BUser::find()->where(['recommend_code' => $code])->one();
        if (is_null($userModel)) {
            return false;
        } else {
            return $userModel->id;
        }
    }

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
        if (empty($user->pwd_salt)) {
            $user->pwd_salt = md5(NOW_TIME . $user->mobile);
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
        $accessToken->content['is_node'] = (bool) $user->node;
        return new ReturnInfo(0, "登录成功", $accessToken->content);
    }

    /**
     * 根据用户密码明文,生成密码hash值
     * @param string $pwdSalt
     * @param string $cleartextPwd
     * @return string
     */
    public static function generateTransPwdHash($pwdSalt, $cleartextPwd)
    {
        return md5($cleartextPwd . $pwdSalt);
    }

    /**
     * 验证用户密码是否正确
     * @param $user
     * @param $pwd
     * @return bool
     */
    public static function validateTransPwd(User $user, $pwd)
    {
        if (self::generateTransPwdHash($user->pwd_salt, $pwd) == $user->trans_password) {
            return true;
        }

        return false;
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
            $userModel->pwd_salt = md5(NOW_TIME . $userModel->mobile);
            // 注册账号为激活状态
            $userModel->status = Buser::STATUS_ACTIVE;
            if (!$userModel->save()) {
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
     * @param $currencyId
     * @return bool
     * @throws \Exception
     * 重置用户积分持仓
     */
    public static function resetCurrency($userId, $currencyId)
    {
        $positionAmount = BUserCurrencyDetail::find()
            ->where(['user_id' => $userId, 'currency_id' => $currencyId])
            ->andWhere(['<>', 'status', BUserCurrencyDetail::$STATUS_EFFECT_FAIL])// 已生效、待生效
            ->sum('amount');
        $effectAmount = BUserCurrencyDetail::find()
            ->where(['user_id' => $userId, 'currency_id' => $currencyId, 'status' => BUserCurrencyDetail::$STATUS_EFFECT_SUCCESS]) // 已生效
            ->sum('amount');
        $frozenAmount = BUserCurrencyFrozen::find()
            ->where(['user_id' => $userId, 'currency_id' => $currencyId, 'status' => BUserCurrencyFrozen::STATUS_FROZEN]) // 冻结
            ->sum('amount');

        $positionAmount = $positionAmount ? round($positionAmount, 8) : 0;
        $effectAmount = $effectAmount ? round($effectAmount, 8) : 0;
        $frozenAmount = $frozenAmount ? round($frozenAmount, 8) : 0;

        $useAmount = round($effectAmount - $frozenAmount, 8);

        //可用数量小于0时，发生数据错误，返回false
        //屏蔽不作判断返回，数据异常也保存，避免异常后数据无法真实重置
//        if ($useAmount < 0 || $positionAmount < $useAmount) {
//            return false;
//        }

        $userCurrencyRes = BUserCurrency::find()
            ->where(['user_id' => $userId, 'currency_id' => $currencyId])
            ->one();

        $time = time();
        if (empty($userCurrencyRes)) {
            // 添加
            $userCurrency = new BUserCurrency();
            $userCurrency->user_id = $userId;
            $userCurrency->currency_id = $currencyId;
            $userCurrency->position_amount = $positionAmount;
            $userCurrency->frozen_amount = $frozenAmount;
            $userCurrency->use_amount= $useAmount;
            $userCurrency->create_time = $time;
            $userCurrency->update_time = $time;
            $sign = $userCurrency->insert();
            if (!$sign) {
                return false;
            }
        } else {
            // 修改
            $sign = BUserCurrency::updateAll(
                ['position_amount' => $positionAmount, 'frozen_amount' => $frozenAmount, 'use_amount' => $useAmount, 'update_time' => $time],
                ['user_id' => $userId, 'currency_id' => $currencyId]
            );
            //数据无变化，影响行数为0也是执行成功的
            if ($sign === false) {
                return false;
            }
        }

        return true;
    }


    public static function resetVoucher($userId)
    {
        $positionAmount = BVoucher::find()
            ->where(['user_id' => $userId])
            ->sum('voucher_num');
        $effectAmount = BVoucherDetail::find()
            ->where(['user_id' => $userId]) // 已生效
            ->sum('amount');

        $positionAmount = $positionAmount ? round($positionAmount, 8) : 0;
        $effectAmount = $effectAmount ? round($effectAmount, 8) : 0;

        $useAmount = round($positionAmount - $effectAmount, 8);

        //可用数量小于0时，发生数据错误，返回false
        //屏蔽不作判断返回，数据异常也保存，避免异常后数据无法真实重置
//        if ($useAmount < 0 || $positionAmount < $useAmount) {
//            return false;
//        }

        $userCurrencyRes = BUserVoucher::find()
            ->where(['user_id' => $userId])
            ->one();

        $time = time();
        if (empty($userCurrencyRes)) {
            // 添加
            $userCurrency = new BUserVoucher();
            $userCurrency->user_id = $userId;
            $userCurrency->position_amount = $positionAmount;
            $userCurrency->surplus_amount = $useAmount;
            $userCurrency->use_amount= $effectAmount;
            $sign = $userCurrency->insert();
            if (!$sign) {
                return false;
            }
        } else {
            // 修改
            $sign = BUserVoucher::updateAll(
                ['position_amount' => $positionAmount, 'surplus_amount' => $useAmount, 'use_amount' => $effectAmount],
                ['user_id' => $userId]
            );
            //数据无变化，影响行数为0也是执行成功的
            if ($sign === false) {
                return false;
            }
        }

        return true;
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

    // 验证推荐人是否可以使用
    public static function checkUserRecommend($user_id, $code)
    {
        $recommend = BUserRecommend::find()->where(['user_id' => $user_id])->one();
        $id = self::validateRemmendCode($code);
        if (!empty($recommend) && $recommend->parent_id != $id) {
            return new ReturnInfo(1, "用户已有推荐人");
        }
        
        if ($id === $user_id) {
            return new ReturnInfo(1, "推荐人不能是自己");
        }
        $user = BUser::find()->where(['id' => $id])->one();
        $parent_arr = explode(',', $user->parent_list);
        if (in_array($user_id, $parent_arr)) {
            return new ReturnInfo(1, "推荐人不能是自己的下级");
        }
        if ($user->parent_list != '') {
            $str = $user->parent_list . ',' . $id;
        } else {
            $str = $id;
        }
        //修改所有下级的上级列表
        $sql = "UPDATE `gr_contest`.`gr_user` SET `parent_list` = CONCAT('".$str."',',',`parent_list`) where `parent_list` like '".$user_id.',%'."' || `parent_list` = $user_id";
        $connection=\Yii::$app->db;
        $command=$connection->createCommand($sql);
        $rowCount=$command->execute();
        // 修改用户自己的上级列表
        $this_user = BUser::find()->where(['id' => $user_id])->one();
        $this_user->parent_list = $str;
        $this_user->save();
        // 添加推荐关系
        if (empty($recommend)) {
            $user_recommend = new BUserRecommend();
            $user_recommend->user_id = $user->id;
            $user_recommend->parent_id = $id;
            if (!$user_recommend->save()) {
                return new ReturnInfo(1, "关联失败", $user_recommend->getFirstErrorText());
            }
        }
        return new ReturnInfo(0, "推荐人关联成功");
    }
}
