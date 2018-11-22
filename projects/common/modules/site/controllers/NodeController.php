<?php

namespace common\modules\site\controllers;

use Exception;
use yii\helpers\ArrayHelper;
use common\components\FuncHelper;
use common\models\business\BNode;
use common\models\business\BUser;
use common\services\UploadService;
use yii\filters\auth\HttpBearerAuth;
use common\models\business\BNodeType;

class NodeController extends BaseController
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

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        if (!$this->validateToken()) {
            die($this->respondJson(1, '接口错误'));
        }
    }

    public function validateToken()
    {
        $token = \Yii::$app->request->post('token');
        if ($token == \Yii::$app->params['quotaToken']) {
            return true;
        }
        return false;
    }

    public function actionTypeList()
    {
        $typeDate = BNodeType::find()
        ->select(['id', 'name'])
        ->active()
        ->asArray()
        ->all();
        
        return $this->respondJson(0, '获取成功', ['list' => $typeDate]);
    }

    /**
     * 所有节点列表
     *
     * @return void
     */
    public function actionList()
    {
        $search = $this->pArray('search');
        $page = $this->pInt('page', 0);
        $page_size = $this->pInt('page_size', 15);
        
        $typeId = $this->pInt('type_id', 0);
        $nodeModel = BNode::find()
        ->alias('n')
        ->select(['n.id', 'n.name', 'u.mobile', 'nt.name type_name', 'IFNULL(n.quota,nt.quota) quota', 'n.create_time'])
        ->joinWith(['nodeType nt', 'user u'], false)
        ->active(BNode::STATUS_ON, 'n.');
        if ($typeId) {
            $nodeModel->where(['n.type_id' => $typeId]);
        }
        // 执行搜索条件查询以及排序
        foreach ($search as $key => $val) {
            if (in_array($key, ['page', 'page_size'])) {
                ${$key} = $val;
                continue;
            }
            if (in_array($key, ['mobile'])) {
                $key = 'u.'.$key; // 指定字段查询 用户字段
                $nodeModel->andFilterWhere(['like', $key, $val]);
            } elseif ($key != 'sort') {
                $key = 'n.'.$key; // 默认查询节点字段
                $nodeModel->andFilterWhere([$key => $val]);
            }
            if ($key == 'sort') {
                $sort = SORT_DESC;
                if (substr($val, 0, 1) == '-') {
                    $val = substr($val, 1);
                    $sort = SORT_ASC;
                }
                $nodeModel->orderBy([$val => $sort]);
                continue;
            }
        }
        $count = $nodeModel->count();
        if ($page) {
            $nodeModel->page($page, $page_size);
        }
        $nodeData = $nodeModel->asArray()
        ->all();
        $data = [
            'page' => $page,
            'page_size' => $page ? $page_size : count($nodeData),
            'count' => $count,
            'list' => $nodeData
        ];
        return $this->respondJson(0, '获取成功', $data, false);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function actionIndex()
    {
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
            // $query->where(['in', 'n.status', [BNode::STATUS_DEL, BNode::STATUS_OFF]]);
            $query->where(['n.status' => BNode::STATUS_ON]);
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

    /**
     * 批量查询节点
     *
     * @return void
     */
    public function actionBatch()
    {
        $mobileList = \Yii::$app->request->post('mobile');
        if (empty($mobileList)) {
            return $this->respondJson(1, '手机号不能为空');
        }
        if (!is_array($mobileList)) {
            $mobileList = (array) $mobileList;
        }
        try {
            foreach ($mobileList as $mobile) {
                if (!FuncHelper::validatMobile($mobile)) {
                    throw new ErrorException($mobile.'：手机号错误');
                }
            }
            $userNode = BUser::find()
            ->select(['n.name', 'u.mobile', 'nt.name type_name'])
            ->alias('u')
            ->joinWith(['node n' => function ($query) {
                // $query->where(['in', 'n.status', [BNode::STATUS_DEL, BNode::STATUS_OFF]]);
                $query->where(['n.status' => BNode::STATUS_ON]);
                $query->joinWith(['nodeType nt'], false);
            }], false)
            ->where(['u.mobile' => $mobileList])
            ->indexBy('mobile')
            ->asArray()
            ->all();
            return $this->respondJson(0, '获取成功', $userNode);
        } catch (\Exception $e) {
            return $this->respondJson(1, $e->getMessage());
        }
    }
}
