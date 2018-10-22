<?php

namespace common\modules\sms\controllers;

use yii\helpers\ArrayHelper;
use common\models\business\BSmsAuth;
use common\models\business\BSmsTemplate;
use common\services\ValidationCodeSmsService;

class SmsController extends BaseController
{
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        $behaviors = [];
        // 需登录才能访问  // 不管哪个module平台访问都需要访问
        $authActions = [
            'user-pay-pass',
            'user-validate-pass',
        ];

        if (isset($parentBehaviors['authenticator']['isThrowException'])) {
            //未登录返回
            if (in_array(\Yii::$app->controller->action->id, $authActions)) {
                $parentBehaviors['authenticator']['isThrowException'] = true;
            }
        }
        return ArrayHelper::merge($parentBehaviors, $behaviors);
    }

    /**
      * 验证手机号码格式是否正确
      */
    private function isValidMobile($mobile)
    {
        $count = preg_match('/^1\d{10}$/', $mobile);
        return $count == 1 ? true : false;
    }
    
    public function actionExistMobile()
    {
        $mobile = $this->pString('mobile');
        $userModel = \Yii::$app->user->identityClass;
        
        if ($this->isValidMobile($mobile) == false) {
            return $this->respondJson(1, '手机号码格式错误');
        }
        $existMobile = $userModel::find()->where(['mobile' => $mobile])->exists();
        return $this->respondJson(0, '校验结果', $existMobile);
    }


    /**
     * 用户登录发送验证码
     *
     * @return void
     */
    public function actionUserLogin()
    {
        $mobile = $this->pString('mobile');

        if ($this->isValidMobile($mobile) == false) {
            return $this->respondJson(1, '手机号码格式错误');
        }
        
        $returnInfo = ValidationCodeSmsService::sendValidationCode($mobile, BSmsTemplate::$TYPE_USER_LOGIN, $userModel->id);
        if ($returnInfo->code != 0) {
            return $this->respondJson($returnInfo->code, $returnInfo->msg);
        }

        return $this->respondJson(0, '发送成功');
    }
    /**
     * 用户修改支付密码发送验证码
     *
     * @return void
     */
    public function actionUserPayPass()
    {
        $userModel = $this->user;
        if (is_null($userModel) || empty($userModel->mobile)) {
            return $this->respondJson(1, '此号码未注册');
        }
        
        $returnInfo = ValidationCodeSmsService::sendValidationCode($userModel->mobile, BSmsTemplate::$TYPE_PAY_PASSWORD, $userModel->id);
        if ($returnInfo->code != 0) {
            return $this->respondJson($returnInfo->code, $returnInfo->msg);
        }

        return $this->respondJson(0, '发送成功');
    }
    
    /**
     * 用户修改支付密码发送验证码
     *
     * @return void
     */
    public function actionTransferPass()
    {
        $userModel = $this->user;
        if (is_null($userModel) || empty($userModel->mobile)) {
            return $this->respondJson(1, '此号码未注册');
        }
        
        $returnInfo = ValidationCodeSmsService::sendValidationCode($userModel->mobile, BSmsTemplate::$TYPE_TRANSFER_GET, $userModel->id);
        if ($returnInfo->code != 0) {
            return $this->respondJson($returnInfo->code, $returnInfo->msg);
        }

        return $this->respondJson(0, '发送成功');
    }
    /**
     * 用户修改支付密码验证码验证---配合 actionUserPayPass 方法一起使用
     *
     * @return void
     */
    public function actionUserValidateVcode()
    {
        $userModel = $this->user;
        $vcode = $this->pString('vcode');
        if (is_null($vcode)) {
            return $this->respondJson(1, '验证码不能为空');
        }

        //手机验证码是否正确, 有效期只有5分钟
        $returnInfo = ValidationCodeSmsService::checkValidateCode(
            $userModel->mobile,
            $vcode,
            BSmsAuth::$TYPE_PAY_PASSWORD,
            false
        );
        if ($returnInfo->code != 0) {
            return $this->respondJson(1, $returnInfo->msg, false);
        }

        return $this->respondJson(0, '验证成功', true);
    }
}
