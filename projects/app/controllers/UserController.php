<?php

namespace app\controllers;

use yii\helpers\ArrayHelper;
use common\components\FuncHelper;
use common\services\SettingService;

class UserController extends BaseController
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
     * 用户中心
     *
     * @return void
     */
    public function actionIndex()
    {
        return $this->respondJson(0, '获取成功');
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
