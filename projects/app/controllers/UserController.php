<?php

namespace app\controllers;

use yii\helpers\ArrayHelper;
use common\services\UserService;
use common\components\FuncHelper;
use common\models\business\BUser;
use common\services\SettingService;
use common\models\business\BUserRecommend;

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

    public function actionRecommendCode()
    {
        $userModel = $this->user;
        if (!(bool) $userModel->recommend_code) {
            $code = UserService::generateRemmendCode(6);
            $userModel->recommend_code = $code;
            if (!$userModel->save()) {
                return $this->respondJson(1, '推荐码生成失败', $userModel->getFirstErrors());
            }
        }
        $data['code'] = $userModel->recommend_code;
        $data['re_code'] = BUserRecommend::find()->where(['parent_id' => $userModel->id])->exists();
        return $this->respondJson(0, '获取成功', $data);
    }

    public function actionAddRecommend()
    {
        $userModel = $this->user;
        $code = $this->pString('code', false);
        if (!preg_match('/^[A-Z0-9]{6}$/i', $code)) {
            return $this->respondJson(1, '推荐码格式错误');
        }
        $parentId = FuncHelper::radixConvert($code);
        if ($parentId === $userModel->id) {
            return $this->respondJson(1, '推荐人不能是自己');
        }
        if (BUserRecommend::find()->where(['user_id' => $userModel->id])->exists()) {
            return $this->respondJson(1, '已添加推荐人');
        }
        $parentModel = BUser::findOne($parentId);
        if (is_null($parentModel)) {
            return $this->respondJson(1, '推荐人不存在');
        }
        $recommendModel = new BUserRecommend();
        $recommendModel->parent_id = (int) $parentId;
        $recommendModel->link('user', $userModel);
        return $this->respondJson(0, '设置成功', $parentId);
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
        ->select(['n.name', 'r.create_time', 'nt.name as type_name', 'r.node_id', 'p.mobile', 'r.parent_id'])
        ->where(['<>', 'r.node_id', 0])
        ->joinWith(['node n' => function ($query) {
            $query->joinWith('nodeType nt');
        }, 'parent p']);
        $data['count'] = $recommendModel->count();
        $data['list'] = $recommendModel
        ->page($page, $pageSize)
        ->asArray()->all();
        foreach ($data['list'] as &$recommend) {
            $recommend['create_time'] = FuncHelper::formateDate($recommend['create_time']);
            $recommend['mobile'] = substr_replace($recommend['mobile'], '****', 3, 4);
            unset($recommend['node']);
            unset($recommend['parent']);
            unset($recommend['node_id']);
            unset($recommend['parent_id']);
        }
        return $this->respondJson(0, '获取成功', $data);
    }
}
