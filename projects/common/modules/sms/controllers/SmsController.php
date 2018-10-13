<?php

namespace common\modules\sms\controllers;

use yii\helpers\ArrayHelper;
use common\models\business\BSmsTemplate;
use common\services\ValidationCodeSmsService;

class SmsController extends BaseController
{
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        $behaviors = [];
        // 需登录才能访问
        $authActions = [
            // 'team-login',
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

        //判断注册号码是否已经使用过
        $user = \common\models\business\Buser::find()->where(['mobile' => $mobile])->exists();

        if (!$user) {
            return $this->respondJson(1, '此号码未注册');
        }
        
        $returnInfo = ValidationCodeSmsService::sendValidationCode($mobile, BSmsTemplate::$TYPE_USER_LOGIN);
        if ($returnInfo->code != 0) {
            return $this->respondJson($returnInfo->code, $returnInfo->msg);
        }

        return $this->respondJson(0, '发送成功');
    }
}
