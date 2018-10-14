<?php

namespace common\modules\captcha\controllers;

use common\components\CaptchaApi;
use yii\helpers\ArrayHelper;

class CaptchaController extends BaseController
{
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        $behaviors = [];
        $authActions = [
            // show
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
      * 显示验证码
      */
    public function actionShow()
    {
        $type = $this->pString('type');
        if (!$type) {
            return $this->respondJson(1, "类型不能为空");
        }

        $respondContent = CaptchaApi::generateCode($type);

        return $this->respondJson(0, "获取成功", $respondContent);
    }


    /**
    * 验证验证码
    *
     */
    public function actionVerifyCode()
    {
        $captchaCode = $this->pString('captcha_code');
        if (!$captchaCode) {
            return $this->respondJson(1, "验证码不能为空");
        }

        $type = $this->pString('type');
        if (!$type) {
            return $this->respondJson(1, "类型不能为空");
        }

        $imageCode = $this->pString('image_code');
        if (!$imageCode) {
            return $this->respondJson(1, "验证码参数不能为空");
        }

        if (CaptchaApi::verifyCode($captchaCode, $type, $imageCode)) {
            return $this->respondJson(0);
        } else {
            return $this->respondJson(1, "验证码不正确");
        }
    }
}
