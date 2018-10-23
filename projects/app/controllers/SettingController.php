<?php

namespace app\controllers;

use yii\helpers\ArrayHelper;
use common\services\NodeService;
use common\components\FuncHelper;
use common\models\business\BNode;
use common\models\business\BVote;
use common\services\SettingService;
use common\models\business\BNodeType;
use common\models\Setting;

class SettingController extends BaseController
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
     * 节点列表
     *
     * @return void
     */
    public function actionIndex()
    {
        SettingService::getKey('vote', 'is_open');
    }
}
