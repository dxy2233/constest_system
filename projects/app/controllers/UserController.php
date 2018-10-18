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

    public function actionRecommend()
    {
        // 返回容器
        $data = [];
        $page = $this->pInt('page', 1);
        $pageSize = $this->pInt('page_size', 15);
        $userModel = $this->user;
        $recommendModel = $userModel->getUserRecommend()
        ->alias('r')
        ->select(['n.name', 'r.create_time', 'nt.name as type_name', 'r.node_id'])
        ->joinWith(['node n' => function ($query) {
            $query->joinWith('nodeType nt');
        }]);
        $data['count'] = $recommendModel->count();
        $data['list'] = $recommendModel
        ->page($page, $pageSize)
        ->asArray()->all();
        foreach ($data['list'] as &$recommend) {
            $recommend['create_time'] = FuncHelper::formateDate($recommend['create_time']);
            unset($recommend['node']);
            unset($recommend['node_id']);
        }
        return $this->respondJson(0, '获取成功', $data);
    }
}
