<?php

namespace common\modules\site\controllers;

use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;
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
     * 上传图片
     */
    public function actionGetSiteInfo()
    {
        $return = ['imgAddress' => \Yii::$app->params['imgAddress']];

        return $this->respondJson(0, '获取成功', $return);
    }
}
