<?php

namespace common\modules\validate\controllers;

use yii\helpers\ArrayHelper;
use common\components\FuncHelper;
use common\models\business\BUser;

class UserController extends BaseController
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
    * 验证手机号是否存在
    *
    * @return void
    */
    public function actionExistMobile()
    {
        $mobile = $this->pString('mobile');
        
        if (!$mobile) {
            return $this->respondJson(1, "手机号不能为空");
        }
        
        if (!FuncHelper::validatMobile($mobile)) {
            return $this->respondJson(1, "手机号格式有误", $mobile);
        }
        
        $UserExists = BUser::find()->where(['mobile' => $mobile])->exists();

        return $this->respondJson(0, '获取成功', $UserExists);
    }
}
