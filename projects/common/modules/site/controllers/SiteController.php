<?php

namespace common\modules\site\controllers;

use yii\helpers\ArrayHelper;
use common\components\FuncHelper;
use common\models\business\BNode;
use common\models\business\BUser;
use common\services\UploadService;
use yii\filters\auth\HttpBearerAuth;

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
     * Undocumented function
     *
     * @return void
     */
    public function actionNode()
    {
        $token = $this->pString('token');
        if ($token !== '2QST9d46soiQpf2Hug8i') {
            return $this->respondJson(1, '接口错误');
        }
        $mobile = $this->pString('mobile');
        if (!$mobile) {
            return $this->respondJson(1, '手机号不能为空');
        }
        if (!FuncHelper::validatMobile($mobile)) {
            return $this->respondJson(1, '手机号格式错误');
        }
        $userNode = BUser::find()
        ->select(['n.name', 'u.mobile', 'nt.name type_name', 'IFNULL(n.quota,nt.quota) quota'])
        ->alias('u')
        ->joinWith(['node n' => function ($query) {
            $query->where(['<>', 'n.status', BNode::STATUS_DEL]);
            $query->joinWith(['nodeType nt'], false);
        }], false)
        ->where(['u.mobile' => $mobile])
        ->asArray()
        ->one();
        if (empty($userNode)) {
            return $this->respondJson(1, '节点不存在');
        }
        return $this->respondJson(0, '获取成功', $userNode);
    }
}
