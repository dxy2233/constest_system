<?php

namespace app\controllers;

use ErrorException;
use yii\helpers\ArrayHelper;
use common\services\NodeService;
use common\components\FuncHelper;
use common\models\business\BNode;
use common\models\business\BVote;
use common\services\SettingService;
use common\models\business\BNodeType;
use common\models\business\BUserOther;

class NodeController extends BaseController
{
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        
        $behaviors = [];
        // 需登录访问
        $authActions = [
            'apply',
            'edit',
        ];

        if (isset($parentBehaviors['authenticator']['isThrowException'])) {
            if (in_array(\Yii::$app->controller->action->id, $authActions)) {
                $parentBehaviors['authenticator']['isThrowException'] = true;
            }
        }

        return ArrayHelper::merge($parentBehaviors, $behaviors);
    }

    /** 无需登录
     * 节点列表
     *
     * @return void
     */
    public function actionIndex()
    {
        $page = $this->pInt('page', false);
        $pageSize = $this->pInt('page_size', 15);
        
        $nodeTypeQuery = BNodeType::find()
        ->select(['id', 'name', 'grt', 'tt', 'bpt'])
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
    /** 无需登录
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
    
    /** 无需登录
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
            $nodeModel = $userModel->node;
            if (is_null($nodeModel)) {
                return $this->respondJson(0, '节点不存在', ['status' => -1, 'status_str' => '节点不存在']);
            } else {
                if ($nodeModel->status !== $nodeModel::STATUS_ON) {
                    $nodeInfo = FuncHelper::arrayOnly($nodeModel->toArray(), ['status', 'statusRemark', 'name']);
                    $nodeInfo['status_str'] = $nodeModel::getStatus($nodeInfo['status']);
                    $nodeInfo['type_id'] = $nodeModel->nodeType->id;
                    $nodeInfo['type_name'] = $nodeModel->nodeType->name;
                    return $this->respondJson(0, '获取成功', $nodeInfo);
                }
                $nodeId = $nodeModel->id;
            }
        }

        $nodeModel = BNode::find()
        ->select(['n.id', 'n.name', 'n.desc', 'n.logo', 'n.scheme', 'n.is_tenure', 'nt.name as type_name', 'n.type_id', 'nt.is_vote', 'n.status'])
        ->alias('n')
        ->joinWith(['nodeType nt'], false)
        ->active(BNode::STATUS_ACTIVE, 'n.')
        ->where(['n.id' => $nodeId])
        ->one();
        if (!is_object($nodeModel)) {
            return $this->respondJson(0, '节点不存在或已关闭', false);
        }
        $votesCount = $nodeModel->getVotes()
        ->select(['COUNT(id) as people_number', 'SUM(vote_number) as vote_number'])
        ->active()
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
        $nodeList['status'] = $nodeModel->status;
        $nodeList['status_str'] = $nodeModel::getStatus($nodeModel->status);
        return $this->respondJson(0, '获取成功', $nodeList);
    }

    /** 无需登录
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
     * 节点申请
     *
     * @return void
     */
    public function actionApply()
    {
        $typeId = $this->pInt('type_id', 1);
        $userModel = $this->user;
        $nodeModel = $userModel->node;
        if (!$userModel->is_identified) {
            return $this->respondJson(1, '未实名认证');
        }
        if ($nodeModel) {
            return $this->respondJson(1, '节点已存在', $nodeModel->toArray());
        }
        $grtNum = $this->pFloat('grt_num', 0);
        $grtAddress = $this->pString('grt_address');
        $ttNum = $this->pFloat('tt_num', 0);
        $ttAddress = $this->pString('tt_address');
        $bptNum = $this->pFloat('bpt_num', 0);
        $bptAddress = $this->pString('bpt_address');
        if ($grtNum <= 0 && $ttNum <= 0 && $bptNum <= 0) {
            return $this->respondJson(1, '质押数量不正确');
        }
        if ($grtNum > 0 && is_null($grtAddress)) {
            return $this->respondJson(1, '贵人通地址不能为空');
        }
        if ($ttNum > 0 && is_null($ttAddress)) {
            return $this->respondJson(1, '茶通地址不能为空');
        }
        if ($bptNum > 0 && is_null($bptAddress)) {
            return $this->respondJson(1, '美食通地址不能为空');
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $maxId = BNode::find()->max('id') + 1;
            $nodeModel = new BNode();
            $nodeModel->status = $nodeModel::STATUS_WAIT;
            $nodeModel->type_id = $typeId;
            $nodeModel->user_id = $userModel->id;
            $nodeModel->name = '节点' . str_pad($maxId, 4, '0', STR_PAD_LEFT);
            $nodeModel->desc = '该节点很懒什么都没有写';
            $nodeModel->scheme = '该节点很懒什么都没有写';
            $nodeModel->grt = $grtNum;
            $nodeModel->tt = $ttNum;
            $nodeModel->bpt = $bptNum;
            if (!$nodeModel->save()) {
                throw new ErrorException($voteModel->getFirstError());
            }
            $otherModel = $userModel->userOther;
            if (!$otherModel) {
                $otherModel = new BUserOther();
                $otherModel->user_id = $userModel->id;
            }
            $otherModel->scenario = 'apply';
            // 应用场景为 申请节点
            $otherModel->attributes = \Yii::$app->request->post();
            if (!$otherModel->validate() || !$otherModel->save()) {
                throw new ErrorException($otherModel->getFirstErrorText());
            }
            $transaction->commit();
            return $this->respondJson(0, '申请成功');
        } catch (\Exception $e) {
            $transaction->rollBack();
            // var_dump($e->getMessage());
            return $this->respondJson(1, $e->getMessage());
        }
    }
    
    /**
     * 节点编辑
     *
     * @return void
     */
    public function actionEdit()
    {
        $userModel = $this->user;
        $nodeModel = $userModel->node;
        if (!$nodeModel) {
            return $this->respondJson(1, '节点不存在', $nodeModel->toArray());
        }
        if ($nodeModel->status !== $nodeModel::STATUS_ON) {
            return $this->respondJson(1, '当前节点状态不能修改', ['status' => $nodeModel::getStatus($nodeModel->status)]);
        }
        $nodeModel->scenario = 'edit';
        $nodeModel->attributes = \Yii::$app->request->post();
        if ($logo = $this->pImage('logo')) {
            $nodeModel->logo = $logo;
        }
        if ($nodeModel->validate() && $nodeModel->save()) {
            return $this->respondJson(0, '修改成功');
        }
        return $this->respondJson(1, $nodeModel->getFirstErrorText());
    }
}
