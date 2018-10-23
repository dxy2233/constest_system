<?php

namespace app\controllers;

use yii\helpers\ArrayHelper;
use common\services\NodeService;
use common\components\FuncHelper;
use common\models\business\BNode;
use common\models\business\BVote;
use common\services\SettingService;
use common\models\business\BNodeType;

class NodeController extends BaseController
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
        $page = $this->pInt('page', false);
        $pageSize = $this->pInt('page_size', 15);
        
        $nodeTypeQuery = BNodeType::find()
        ->select(['id', 'name'])
        ->where(['is_order' => BNodeType::STATUS_ACTIVE])
        ->active();
        if ($page) {
            $data['count'] = $nodeTypeQuery->count();
            $nodeTypeQuery->page($page, $pageSize);
        }
        $nodeType = $nodeTypeQuery->orderBy(['sort' => SORT_ASC])->asArray()->all();
        $data = $page ? array_merge($data, ['list' => $nodeType]) : $nodeType;
        return $this->respondJson(0, '获取成功', $data);
    }
    /**
     * 节点列表 选举结果
     *
     * @return void
     */
    public function actionVote()
    {
        $page = $this->pInt('page', false);
        $pageSize = $this->pInt('page_size', 15);
        // 返回数据容器
        $data = [];
        $nodeTypeId = $this->pInt('id', 0);
        if (empty($nodeTypeId)) {
            return $this->respondJson(1, '节点类型ID不能为空');
        }
        $isOpen = SettingService::get('vote', 'is_open');
        if (!is_object($isOpen) && !(bool) $isOpen->value) {
            return $this->respondJson(1, "投票未启用");
        }

        // 不传递page 则为首页
        if (!$page) {
            $nodeNumberModel = (int) SettingService::get('node', 'index_node_number')->value;
            $nodeNumber = BNode::INDEX_NUMBER;
            if ($nodeNumberModel) {
                $nodeNumber = intval($nodeNumberModel);
            }
            $data = NodeService::getNodeList($nodeTypeId, 1, $nodeNumber);
        } else {
            $data['list'] = NodeService::getNodeList($nodeTypeId, $page, $pageSize);
            $data['count'] = NodeService::$number;
        }
        return $this->respondJson(0, '获取成功', $data);
    }
    
    /**
     * 节点详情
     *
     * @return void
     */
    public function actionInfo()
    {
        $userModel = $this->user;
        $nodeId = $this->pInt('id', 0);

        if (empty($nodeId) && is_null($userModel)) {
            return $this->respondJson(1, '节点ID不能为空');
        }
        
        if (empty($nodeId) && !is_null($userModel)) {
            $nodeId = $userModel->node->id;
        }

        $nodeModel = BNode::find()
        ->select(['n.id', 'n.name', 'n.desc', 'n.logo', 'n.scheme', 'n.is_tenure', 'nt.name as type_name', 'n.type_id', 'nt.is_vote'])
        ->alias('n')
        ->joinWith(['nodeType nt'], false)
        ->active(BNode::STATUS_ACTIVE, 'n.')
        ->where(['n.id' => $nodeId])
        ->one();
        if (!is_object($nodeModel)) {
            return $this->respondJson(1, '节点不存在或已关闭');
        }
        $votesCount = $nodeModel->getVotes()
        ->select(['COUNT(id) as people_number', 'SUM(vote_number) as vote_number'])
        ->asArray()
        ->one();
        
        $nodeList = ArrayHelper::toArray($nodeModel);
        $voteUser = \common\services\NodeService::getPeopleNum((array) $nodeList['id']);
        $nodeList['people_number'] = $voteUser[$nodeList['id']] ?? '0';
        $nodeList['vote_number'] = $votesCount['vote_number'] ?? '0';
        $nodeList['logo'] = $nodeModel->logoText;
        $nodeList['is_tenure'] = $nodeModel->isTenureText;
        $nodeList['type_name'] = $nodeModel->type_name;
        $nodeList['is_vote'] = (bool) $nodeModel->is_vote;
        return $this->respondJson(0, '获取成功', $nodeList);
    }

    /**
     * 节点投票明细
     *
     * @return void
     */
    public function actionVoteDetail()
    {
        $data = [];
        $nodeId = $this->pInt('id', 0);
        $nodeShowType = $this->pString('type', 'log');
        $page = $this->pInt('page', 1);
        $pageSize = $this->pInt('page_size', 15);
        if (empty($nodeId)) {
            return $this->respondJson(1, '节点ID不能为空');
        }
        $voteModel = BVote::find()
        ->select(['user_id', 'node_id', 'create_time', 'type', 'status'])
        ->with('user')
        ->active()
        ->where(['node_id' => $nodeId]);
        if ($nodeShowType !== 'log') {
            $voteModel->addSelect(['SUM(vote_number) as vote_number']);
            $voteModel->orderBy(['vote_number' => SORT_DESC]);
            $voteModel->groupBy('user_id');
        } else {
            $voteModel->addSelect(['vote_number']);
            $voteModel->orderBy(['create_time' => SORT_DESC]);
        }
        $data['count'] = $voteModel->count();
        $voteModel->page($page, $pageSize);
        $voteDataModel = $voteModel->all();

        $voteData = [];
        foreach ($voteDataModel as $key => $vote) {
            $vote->create_time = $vote->createTimeText;
            $addData = [
                'type_str' => BVote::getType($vote->type),
                'status_str' => BVote::getStatus($vote->status),
                'mobile' => substr_replace($vote->user->mobile, '****', 3, 4),
            ];
            $vote = $vote->toArray();
            FuncHelper::arrayForget($vote, ['type', 'status', 'consume', 'user_id', 'node_id']);
            $voteData[$key] = array_merge($vote, $addData);
        }
        $data['list'] = $voteData;
        return $this->respondJson(0, '获取成功', $data);
    }

    /**
     * 所有节点权益详情
     *
     * @return void
     */
    public function actionAllRuleInfo()
    {
        // 节点类型
        $typeId = $this->pInt('id');
        $TypeRuleModel = BTypeRuleContrast::find()
        ->where(['type_id' => $typeId]);
    }
}
