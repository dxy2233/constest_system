<?php

namespace app\controllers;

use yii\helpers\ArrayHelper;
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
        $nodeId = $this->pInt('id', 0);
        if (empty($nodeId)) {
            return $this->respondJson(1, '节点ID不能为空');
        }
        $isOpen = SettingService::get('vote', 'is_open');
        if (!is_object($isOpen) && !(bool) $isOpen->value) {
            return $this->respondJson(1, "投票未启用");
        }
        // 刷新时间获取，更新时间
        $endUpdate = SettingService::get('vote', 'end_update_time');
        if (!is_object($endUpdate) && empty($endUpdate->value)) {
            return $this->respondJson(1, "投票更新时间未设定");
        }
        $updateTime = (int) $endUpdate->value;
        $nodeType = BNodeType::findOne($nodeId);
        if (!is_object($nodeType)) {
            return $this->respondJson(1, '节点不存在');
        }
        $nodeModel = BNode::find()
        ->select(['id', 'name', 'desc', 'logo', 'is_tenure'])
        ->active()
        ->with('votes');
        // 不传递page 则为首页
        if (!$page) {
            $nodeNumberModel = SettingService::get('vote', 'index_node_number');
            $nodeNumber = BNode::INDEX_NUMBER;
            if (is_object($nodeNumberModel)) {
                $nodeNumber = intval($nodeNumberModel->value);
            }
            $nodeModel->limit($nodeNumber);
        } else {
            $data['count'] = $nodeModel->count();
            $nodeModel->page($page, $pageSize);
        }
        $nodeModelList = $nodeModel->all();
        $nodeList = ArrayHelper::toArray($nodeModelList);
        foreach ($nodeModelList as $key => $node) {
            $number = $node->getVotes()
            ->select(['COUNT(id) as count_number', 'SUM(vote_number) as vote_number'])
            ->asArray()
            ->one();
            $number['vote_number'] = $number['vote_number'] ?? '0';
            $nodeList[$key] = array_merge($nodeList[$key], $number);
            $nodeList[$key]['logo'] = $node->logoText;
            $nodeList[$key]['is_tenure'] = $node->isTenureText;
        }
        if (!$page) {
            $data = $nodeList;
        } else {
            $data['countTime'] = FuncHelper::formateDate($updateTime);
            $data['list'] = $nodeList;
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
        $nodeId = $this->pInt('id', 0);
        if (empty($nodeId)) {
            return $this->respondJson(1, '节点ID不能为空');
        }

        $nodeModel = BNode::find()
        ->select(['id', 'name', 'desc', 'logo', 'scheme', 'is_tenure'])
        ->active()
        ->with('votes')
        ->one();
        if (!is_object($nodeModel)) {
            return $this->respondJson(1, '节点不存在或已关闭');
        }
        $votesCount = $nodeModel->getVotes()
        ->select(['COUNT(id) as people_number', 'SUM(vote_number) as vote_number'])
        ->asArray()
        ->one();
        $nodeList = ArrayHelper::toArray($nodeModel);
        $nodeList['people_number'] = $votesCount['people_number'] ?? '0';
        $nodeList['vote_number'] = $votesCount['vote_number'] ?? '0';
        $nodeList['logo'] = $nodeModel->logoText;
        $nodeList['is_tenure'] = $nodeModel->isTenureText;
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
        // ->select(['vote_number', 'create_time'])
        ->active()
        ->where(['node_id' => $nodeId]);
        $data['count'] = $voteModel->count();
        $voteModel->page($page, $pageSize);
        if ($nodeShowType === 'log') {
            $voteData = $voteModel->all();
        } else {

        }
        if (!is_object(reset($voteData))) {
            return $this->respondJson(0, '记录为空');
        }
        foreach ($voteData as $vote) {
            // $vote->type = 
            $vote->create_time = $vote->createTimeText;
        }
        $data['list'] = ArrayHelper::toArray($voteData);
        return $this->respondJson(0, '获取成功', $data);
    }
}
