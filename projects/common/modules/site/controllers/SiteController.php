<?php

namespace common\modules\site\controllers;

use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;
use common\components\FuncHelper;
use common\services\UploadService;

class SiteController extends BaseController
{
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        $behaviors = [];
        $authActions = [
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
     * 获取网站信息
     */

}
