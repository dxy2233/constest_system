<?php

namespace app\controllers;

use yii\helpers\ArrayHelper;
use common\components\FuncHelper;
use common\services\SettingService;

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
        $userModel = $this->user;
        if (!empty($userModel->trans_password)) {
            return $this->respondJson(1, '支付密码已存在');
        }
        if (!$payPass) {
            return $this->respondJson(1, '支付密码不能为空');
        }
        $passLen = (int) SettingService::get('user', 'trans_pass_num')->value;
        if ($passLen == strlen($payPass)) {
            $userModel->trans_password = \Yii::$app->getSecurity()->encryptByPassword($payPass, \Yii::$app->params['secretKey']);
        } else {
            return $this->respondJson(1, '支付密码长度不够');
        }
        if (!$userModel->save()) {
            return $this->respondJson(1, '支付密码保存失败', $userModel->getFirstErrorText());
        }
        return $this->respondJson(0, '支付密码设置成功');
    }
    /**
     * 创建支付密码
     *
     * @return void
     */
    public function actionUpdatePass()
    {
        $payPass = $this->pInt('pass', false);
        $userModel = $this->user;
        if (!empty($userModel->trans_password)) {
            return $this->respondJson(1, '支付密码已存在');
        }
        if (!$payPass) {
            return $this->respondJson(1, '支付密码不能为空');
        }
        $passLen = (int) SettingService::get('user', 'trans_pass_num')->value;
        if ($passLen == strlen($payPass)) {
            $userModel->trans_password = \Yii::$app->getSecurity()->encryptByPassword($payPass, \Yii::$app->params['secretKey']);
        } else {
            return $this->respondJson(1, '支付密码长度不够');
        }
        if (!$userModel->save()) {
            return $this->respondJson(1, '支付密码保存失败', $userModel->getFirstErrorText());
        }
        return $this->respondJson(0, '支付密码设置成功');
    }

    /**
     * 设置支付密码
     *
     * @return void
     */
    public function actionAddPayPass()
    {
    }
}
