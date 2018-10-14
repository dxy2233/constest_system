<?php

namespace app\controllers;

use yii\helpers\ArrayHelper;
use common\components\FuncHelper;
use common\services\SettingService;

class WalletController extends BaseController
{
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        
        $behaviors = [];
        // 需登录访问
        $authActions = [
        ];

        if (isset($parentBehaviors['authenticator']['isThrowException'])) {
            if (in_array(\Yii::$app->controller->action->id, $authActions)) {
                $parentBehaviors['authenticator']['isThrowException'] = true;
            }
        }

        return ArrayHelper::merge($parentBehaviors, $behaviors);
    }

    /**
     * 贡献榜
     *
     * @return void
     */
    public function actionIndex()
    {
        echo 1;
        exit;
    }
    /**
     * 贡献榜
     *
     * @return void
     */
    public function actionImport()
    {
        echo 1;
        exit;
    }
}
