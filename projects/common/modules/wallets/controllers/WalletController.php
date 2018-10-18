<?php

namespace common\modules\wallet\controllers;

use yii\helpers\ArrayHelper;

class WalletController extends BaseController
{
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        $behaviors = [];
        // 需登录才能访问
        $authActions = [
            // 'exist-mobile',
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
    * 获取钱包列表
    *
    * @return void
    */
    public function actionWallet()
    {
        return $this->respondJson(0, '获取成功', Yii::$app->params['wallet']);
    }
}
