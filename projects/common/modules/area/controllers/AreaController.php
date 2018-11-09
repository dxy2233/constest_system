<?php

namespace common\modules\area\controllers;

use yii\helpers\ArrayHelper;
use common\models\business\BArea;
use common\models\business\BSmsTemplate;
use common\services\ValidationCodeSmsService;

class AreaController extends BaseController
{
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        $behaviors = [];
        // 需登录才能访问  // 不管哪个module平台访问都需要访问
        $authActions = [
            'user-pay-pass',
            'user-validate-pass',
        ];

        if (isset($parentBehaviors['authenticator']['isThrowException'])) {
            //未登录返回
            if (in_array(\Yii::$app->controller->action->id, $authActions)) {
                $parentBehaviors['authenticator']['isThrowException'] = true;
            }
        }
        return ArrayHelper::merge($parentBehaviors, $behaviors);
    }

    public function actionGetCityList()
    {
        $id = $this->pInt('id', 1);
        $res = BArea::find()->where(['parentid'=>$id])->all();
        
        if (count($res) == 1) {
            $res = BArea::find()->where(['parentid'=>$res[0]->id])->all();
        }
        return $this->respondJson(0, null, \yii\helpers\ArrayHelper::map($res, 'id', 'areaname'));
    }

    public function actionGetAddressName()
    {
        $id = $this->pInt('id');
        if (empty($id)) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $data = BArea::find()->where(['id' => $id])->one();
        return $this->respondJson(0, '获取成功', $data->areaname);
    }
}
