<?php

namespace common\modules\upload\controllers;

use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;
use common\services\UploadService;
use common\components\FuncHelper;

class UploadController extends BaseController
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
    public function actionImage()
    {
        $type = $this->pString('type');
        if (empty($type)) {
            $type = 'uploads';
        }
        $result = UploadService::uploadImage($type);
        $url = FuncHelper::getImageUrl($result->content);
        return $this->respondJson($result->code, $result->msg, $url);
    }
}
