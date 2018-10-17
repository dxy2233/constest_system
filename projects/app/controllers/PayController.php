<?php

namespace app\controllers;

use yii\helpers\ArrayHelper;
use common\services\UserService;
use common\components\FuncHelper;
use common\services\SettingService;
use common\models\business\BSmsAuth;
use common\models\business\BUserLog;

class PayController extends BaseController
{
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        
        $behaviors = [];
        // 无需需登录访问 为空则所有相关接口都需登录后访问
        $authActions = [
        ];

        if (isset($parentBehaviors['authenticator']['isThrowException'])) {
            if (!in_array(\Yii::$app->controller->action->id, $authActions)) {
                $parentBehaviors['authenticator']['isThrowException'] = true;
            }
        }

        return ArrayHelper::merge($parentBehaviors, $behaviors);
    }

    /**
     * 创建支付密码
     *
     * @return void
     */
    public function actionExistPass()
    {
        $userModel = $this->user;
        return $this->respondJson(0, '获取成功', !empty($userModel->trans_password));
    }


    /**
     * 创建支付密码
     *
     * @return void
     */
    public function actionCreatePass()
    {
        $payPass = $this->pInt('pass', false);
        $rePass = $this->pInt('repass', false);
        $userModel = $this->user;
        if (!empty($userModel->trans_password)) {
            return $this->respondJson(1, '支付密码已存在');
        }
        if (!$payPass) {
            return $this->respondJson(1, '支付密码不能为空');
        }
        if ($payPass !== $rePass) {
            return $this->respondJson(1, '两次支付密码不一致');
        }
        $passLen = (int) SettingService::get('user', 'trans_pass_num')->value;
        if ($passLen == strlen($payPass)) {
            $userModel->trans_password = FuncHelper::encryptPassWordHash($payPass);
        } else {
            return $this->respondJson(1, '支付密码长度不够');
        }
        if (!$userModel->save()) {
            return $this->respondJson(1, '支付密码保存失败', $userModel->getFirstErrorText());
        }
        return $this->respondJson(0, '支付密码设置成功');
    }
    /**
     * 修改支付密码
     *
     * @return void
     */
    public function actionUpdatePass()
    {
        $payPass = $this->pInt('pass', false);
        $rePass = $this->pInt('repass', false);
        $oldpass = $this->pInt('oldpass', false);
        $userModel = $this->user;
        if (!$payPass) {
            return $this->respondJson(1, '支付密码不能为空');
        }
        if ($payPass !== $rePass) {
            return $this->respondJson(1, '两次支付密码不一致');
        }
        $validateOldPass = FuncHelper::validatePassWordHash($oldpass, $userModel->trans_password);
        if ($validateOldPass) {
            return $this->respondJson(1, '原密码错误');
        }
        $passLen = (int) SettingService::get('user', 'trans_pass_num')->value;
        if ($passLen == strlen($payPass)) {
            $userModel->trans_password = FuncHelper::encryptPassWordHash($payPass);
        } else {
            return $this->respondJson(1, '支付密码长度不够');
        }
        if (!$userModel->save()) {
            return $this->respondJson(1, '支付密码保存失败', $userModel->getFirstErrorText());
        }
        // 写日志
        UserService::writeUserLog($userModel->id, BUserLog::$TYPE_ALERT_TRANS_PWD, BUserLog::$STATUS_SUCCESS, '登录成功', $userModel->last_login_ip);
        return $this->respondJson(0, '支付密码设置成功');
    }

    /**
     * 重置支付密码
     *
     * @return void
     */
    public function actionResetPass()
    {
        $payPass = $this->pInt('pass', false);
        $rePass = $this->pInt('repass', false);
        $vcode = $this->pString('vcode');
        $userModel = $this->user;
        if (!$payPass) {
            return $this->respondJson(1, '支付密码不能为空');
        }
        if ($payPass !== $rePass) {
            return $this->respondJson(1, '两次支付密码不一致');
        }
        // 短信验证码
        if (\Yii::$app->params['sendSms']) {
            //手机验证码是否正确, 有效期只有5分钟
            $returnInfo = ValidationCodeSmsService::checkValidateCode(
                $userModel->mobile,
                $vcode,
                BSmsAuth::$TYPE_PAY_PASSWORD
            );
            if ($returnInfo->code != 0) {
                return $this->respondJson(1, $returnInfo->msg);
            }
        }
        $passLen = (int) SettingService::get('user', 'trans_pass_num')->value;
        if ($passLen == strlen($payPass)) {
            $userModel->trans_password = FuncHelper::encryptPassWordHash($payPass);
        } else {
            return $this->respondJson(1, '支付密码长度不够');
        }
        if (!$userModel->save()) {
            return $this->respondJson(1, '支付密码保存失败', $userModel->getFirstErrorText());
        }
        // 写日志
        UserService::writeUserLog($userModel->id, BUserLog::$TYPE_RESET_TRANS_PWD, BUserLog::$STATUS_SUCCESS, '登录成功', $userModel->last_login_ip);
        return $this->respondJson(0, '支付密码设置成功');
    }
    /**
     * 验证支付密码
     *
     * @return void
     */
    public function actionValidatePass()
    {
        $payPass = $this->pInt('pass', false);
        if (!$payPass) {
            return $this->respondJson(1, '支付密码不能为空');
        }
        $userModel = $this->user;
        $pass = FuncHelper::validatePassWordHash($payPass, $userModel->trans_password);
        return $this->respondJson(0, '校验结果', $pass);
    }
}
