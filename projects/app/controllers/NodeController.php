<?php

namespace app\controllers;

use ErrorException;
use yii\helpers\ArrayHelper;
use common\services\NodeService;
use common\services\UserService;
use common\components\FuncHelper;
use common\models\business\BNode;
use common\models\business\BUser;
use common\models\business\BVote;
use common\services\SettingService;
use common\models\business\BNodeType;
use common\models\business\BUserOther;
use common\models\business\BNodeUpgrade;

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
            'type-list',
            'recommend',
            'recommend-mobile',
            'upgrade',
            'upgrade-status',
            'activation',
            'has-recommend',
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
            $newNodeGrade = $userModel->newNodeGrade;
            if (!$nodeModel && !$newNodeGrade) {
                if (!$userModel->nodeExtend) {
                    return $this->respondJson(0, '节点不存在', ['status' => -1, 'status_str' => '节点不存在']);
                } else {
                    return $this->respondJson(0, '节点未激活', ['status' => 0, 'status_str' => '节点未激活']);
                }
            } else {
                $nodeInfoModel = $nodeModel ?? $newNodeGrade;
                if ($nodeInfoModel->status !== $nodeInfoModel::STATUS_ACTIVE) {
                    $nodeInfo = FuncHelper::arrayOnly($nodeInfoModel->toArray(), ['status', 'status_remark', 'name']);
                    $nodeInfo['status_str'] = $nodeInfoModel::getStatus($nodeInfo['status']);
                    $nodeType = $nodeInfoModel->nodeType;
                    $nodeInfo['type_id'] = $nodeType->id;
                    $nodeInfo['type_name'] = $nodeType->name;
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

    /** 无需登录
     * 节点类型列表
     *
     * @return void
     */
    public function actionTypeList()
    {
        $nodeTypeQuery = BNodeType::find()
        ->select(['id', 'name', 'grt', 'tt', 'bpt'])
        ->active();
        $nodeType = $nodeTypeQuery->orderBy(['sort' => SORT_ASC])->asArray()->all();
        return $this->respondJson(0, '获取成功', $nodeType);
    }

    /**
     * 节点申请
     *
     * @return void
     */
    public function actionApply()
    {
        $typeId = $this->pInt('type_id', 1);
        $weixin = $this->pString('weixin');
        $recommendMobile = $this->pString('recommend_mobile');
        if (!$weixin) {
            return $this->respondJson(1, '微信号不能为空');
        }
        $userModel = $this->user;
        $nodeModel = $userModel->node;
        if (!$userModel->is_identified) {
            return $this->respondJson(1, '未实名认证');
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
        $reParentId = null;
        // 判断推荐人 如果是超级节点则跳过判断，不进行关系数据绑定
        if ($typeId !== 1 && !is_null($recommendMobile)) {
            if (!FuncHelper::validatMobile($recommendMobile)) {
                return $this->respondJson(1, '手机号错误');
            }
            if ($recommendMobile == $userModel->mobile) {
                return $this->respondJson(1, '推荐人不能是自己');
            }
            $recommendUser = BUser::find()->where(['mobile' => $recommendMobile])->one();
            if (!$recommendUser) {
                return $this->respondJson(1, '推荐人用户不存在');
            }
            $recommendNodeModel = $recommendUser->getNode()
            ->active()
            ->one();
            if (!$recommendNodeModel) {
                return $this->respondJson(1, '推荐人不是节点');
            }
            // 推荐人不能是微店节点
            if ($recommendNodeModel->type_id == 5) {
                return $this->respondJson(1, '推荐人不能是微店节点');
            }
            // 获取节点推荐关系
            $nodeRecommend = $recommendUser->nodeRecommend;
            $parent_arr = $nodeRecommend ? explode(',', $nodeRecommend->parent_list) : [];
            if (in_array($userModel->id, $parent_arr)) {
                return $this->respondJson(1, '推荐人不能是自己的下级');
            }
            $reParentId = $recommendUser->id;
        }
        
        if ($nodeModel && in_array($nodeModel->status, [$nodeModel::STATUS_ON, $nodeModel::STATUS_WAIT, $nodeModel::STATUS_OFF])) {
            return $this->respondJson(1, '节点已存在');
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $nodeUpgradeModel = new BNodeUpgrade();
            $maxId = BNodeUpgrade::find()->max('id') + 1;
            $nodeUpgradeData = [
                'weixin' => $weixin ?? '未设置',
                'desc' => '该节点很懒什么都没有写',
                'scheme' => '该节点很懒什么都没有写',
                'name' => '节点' . str_pad($maxId, 4, '0', STR_PAD_LEFT),
                'user_id' => $userModel->id,
                'status' => $nodeUpgradeModel::STATUS_WAIT,
                'old_type' => 0,
                'type_id' => $typeId,
                'parent_id' => $reParentId ?? 0,
                'grt' => $grtNum,
                'tt' => $ttNum,
                'bpt' => $bptNum,
                'grt_address' => $grtAddress,
                'tt_address' => $ttAddress,
                'bpt_address' => $bptAddress,
            ];
            $nodeUpgradeModel->attributes = array_filter($nodeUpgradeData, function($item) {
                return !is_null($item);
            });
            if (!$nodeUpgradeModel->save()) {
                throw new ErrorException($voteModel->getFirstError());
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
    /**
     * 获取推荐人信息
     * 手机号获取
     *
     * @return void
     */
    public function actionRecommendMobile()
    {
        $userModel = $this->user;
        $mobile = $this->pString('mobile');
        if (!FuncHelper::validatMobile($mobile)) {
            return $this->respondJson(1, '手机号错误');
        }
        if (!$mobile) {
            return $this->respondJson(1, '手机号不能为空');
        }
        if ($mobile == $userModel->mobile) {
            return $this->respondJson(1, '推荐人不能是自己');
        }
        $recommendUser = BUser::find()->where(['mobile' => $mobile])->one();
        if (!$recommendUser) {
            return $this->respondJson(1, '推荐人用户不存在');
        }
        $recommendNodeModel = $recommendUser->getNode()
        ->active()
        ->one();
        if (!$recommendNodeModel) {
            return $this->respondJson(1, '推荐人不是节点');
        }
        $nodeRecommend = $recommendUser->nodeRecommend;
        $parent_arr = $nodeRecommend ? explode(',', $nodeRecommend->parent_list) : [];
        if (in_array($userModel->id, $parent_arr)) {
            return $this->respondJson(1, '推荐人不能是自己的下级');
        }
        $data = [
            'mobile' => $mobile,
            'realname' => $recommendUser->identify->realname,
            'node_name' => $recommendNodeModel->name,
            'type_id' => $recommendNodeModel->type_id,
            'node_type' => $recommendNodeModel->nodeType->name,
        ];
        return $this->respondJson(0, '获取成功', $data);
    }

    /**
     * 获取推荐记录
     *
     * @return void
     */
    public function actionRecommend()
    {
        // 返回容器
        $data = [];
        $page = $this->pInt('page', 1);
        $pageSize = $this->pInt('page_size', 15);
        $userModel = $this->user;
        $recommendModel = $userModel->getParentNodeRecommend()
        ->alias('r')
        ->select(['r.id', 'r.create_time', 'nt.name as type_name', 'u.mobile'])
        ->joinWith(['node n' => function ($query) {
            $query->joinWith('nodeType nt', false);
        }, 'user u'], false);
        $data['count'] = $recommendModel->count();
        $data['list'] = $recommendModel
        ->orderBy(['r.create_time' => SORT_DESC])
        ->page($page, $pageSize)
        ->asArray()->all();
        foreach ($data['list'] as &$recommend) {
            $recommend['create_time'] = FuncHelper::formateDate($recommend['create_time']);
            $recommend['mobile'] = substr_replace($recommend['mobile'], '****', 3, 4);
            $recommend['type_name'] = $recommend['type_name'];
        }
        return $this->respondJson(0, '获取成功', $data);
    }
    /**
     * 节点升级
     *
     * @return void
     */
    public function actionUpgrade()
    {
        $userModel = $this->user;
        $nodeModel = $userModel->node;
        $typeId = $this->pInt('type_id');
        $recommendMobile = $this->pString('recommend_mobile');
        if (!$nodeModel) {
            return $this->respondJson(1, '节点不存在');
        }
        if ($typeId >= $nodeModel->type_id) {
            return $this->respondJson(1, '节点类型不能低于当前节点');
        }
        // 勾选清除推荐关系
        $hasRemoveRecommend = $this->pString('remove_recommend');
        if ($typeId == 1 && $hasRemoveRecommend !== 'true') {
            return $this->respondJson(1, '需勾选清除推荐关系');
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
        $reParentId = null;
        // 判断推荐人 如果是超级节点则跳过判断，不进行关系数据绑定
        if ($typeId !== 1 && !is_null($recommendMobile)) {
            if (!FuncHelper::validatMobile($recommendMobile)) {
                return $this->respondJson(1, '手机号错误');
            }
            if ($recommendMobile == $userModel->mobile) {
                return $this->respondJson(1, '推荐人不能是自己');
            }
            $recommendUser = BUser::find()->where(['mobile' => $recommendMobile])->one();
            if (!$recommendUser) {
                return $this->respondJson(1, '推荐人用户不存在');
            }
            $recommendNodeModel = $recommendUser->getNode()
            ->active()
            ->one();
            if (!$recommendNodeModel) {
                return $this->respondJson(1, '推荐人不是节点');
            }
            // 推荐人不能是微店节点
            if ($recommendNodeModel->type_id == 5) {
                return $this->respondJson(1, '推荐人不能是微店节点');
            }
            // 获取节点推荐关系
            $nodeRecommend = $recommendUser->nodeRecommend;
            $parent_arr = $nodeRecommend ? explode(',', $nodeRecommend->parent_list) : [];
            if (in_array($userModel->id, $parent_arr)) {
                return $this->respondJson(1, '推荐人不能是自己的下级');
            }
            $reParentId = $recommendUser->id;
        }
        
        $grtNum = $this->pFloat('grt_num', 0);
        $grtAddress = $this->pString('grt_address');
        $ttNum = $this->pFloat('tt_num', 0);
        $ttAddress = $this->pString('tt_address');
        $bptNum = $this->pFloat('bpt_num', 0);
        $bptAddress = $this->pString('bpt_address');
        
        $transaction = \Yii::$app->db->beginTransaction();
        try{
            $nodeUpgradeModel = new BNodeUpgrade();
            $userUpgradeQuery = $userModel->getNewNodeGrade()
            ->active($nodeUpgradeModel::STATUS_WAIT);
            if ($userUpgradeQuery->exists()) {
                throw new ErrorException('节点正在升级中');
            }
            $nodeUpgradeData = [
                'user_id' => $userModel->id,
                'status' => $nodeUpgradeModel::STATUS_WAIT,
                'old_type' => $nodeModel->type_id,
                'type_id' => $typeId,
                'parent_id' => $reParentId ?? 0,
                'grt' => $grtNum,
                'tt' => $ttNum,
                'bpt' => $bptNum,
                'grt_address' => $grtAddress,
                'tt_address' => $ttAddress,
                'bpt_address' => $bptAddress,
            ];
            $nodeUpgradeModel->attributes = array_filter($nodeUpgradeData, function($item) {
                return !is_null($item);
            });
            if (!$nodeUpgradeModel->save()) {
                throw new ErrorException($nodeUpgradeModel->getFirstError());
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
     * 节点升级状态
     */
    public function actionUpgradeStatus()
    {
        $userModel = $this->user;
        $newNodeGradeModel = $userModel->newNodeGrade;
        if (!$newNodeGradeModel) {
            return $this->respondJson(1, '没有申请记录');
        }
        $newNodeGrade = FuncHelper::arrayOnly($newNodeGradeModel->toArray(), ['status', 'status_remark']);
        $newNodeGrade['status_str'] = $newNodeGradeModel::getStatus($newNodeGrade['status']);
        $newNodeGrade['status_remark'] = $newNodeGrade['status_remark'] ?: $newNodeGradeModel::getStatus($newNodeGrade['status']);
        return $this->respondJson(0, '获取成功', $newNodeGrade);
    }

    /**
     * 节点激活
     *
     * @return void
     */
    public function actionActivation()
    {
        $userModel = $this->user;
        if (!$userModel->is_identified) {
            return $this->respondJson(1, '未实名认证');
        }
        $nodeModel = $userModel->node;
        if ($nodeModel && in_array($nodeModel->status, [$nodeModel::STATUS_ON, $nodeModel::STATUS_WAIT, $nodeModel::STATUS_OFF])) {
            return $this->respondJson(1, '节点已存在');
        }
        if (!$nodeExtend = $userModel->nodeExtend) {
            return $this->respondJson(1, '激活节点不存在');
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $statusRemark = '节点激活';
            $desc = '该节点很懒什么都没有写';
            $scheme = '该节点很懒什么都没有写';
            $nodeUpgradeModel = new BNodeUpgrade();
            $nodeUpgradeModel->old_type = 0;
            $nodeUpgradeModel->type_id = $nodeExtend->type_id;
            $nodeUpgradeModel->user_id = $userModel->id;
            $nodeUpgradeModel->name = $nodeExtend->name;
            $nodeUpgradeModel->desc = '该节点很懒什么都没有写';
            $nodeUpgradeModel->scheme = '该节点很懒什么都没有写';
            $nodeUpgradeModel->parent_id = 0;
            if (!$nodeUpgradeModel->save()) {
                throw new ErrorException($voteModel->getFirstError());
            }
            $nodeModel = new BNode();
            $nodeModel->user_id = $userModel->id;
            $nodeModel->type_id = $nodeExtend->type_id;
            $nodeModel->name = $nodeExtend->name;
            $nodeModel->desc = $desc;
            $nodeModel->scheme = $scheme;
            if (!$nodeModel->save()) {
                throw new ErrorException($voteModel->getFirstError());
            }
            // 独立出状态修改以及状态备注 在审核时间上面加10秒 体现出状态修改痕迹
            $nodeUpgradeModel->status = $nodeUpgradeModel::STATUS_ACTIVE;
            $nodeUpgradeModel->status_remark = $statusRemark;
            $nodeUpgradeModel->examine_time = time() + 10;
            if (!$nodeUpgradeModel->save()) {
                throw new ErrorException($voteModel->getFirstError());
            }
            $nodeModel->status = $nodeModel::STATUS_ON;
            $nodeModel->status_remark = $statusRemark;
            $nodeModel->examine_time = time() + 10;
            if (!$nodeModel->save()) {
                throw new ErrorException($voteModel->getFirstError());
            }
            $transaction->commit();
            return $this->respondJson(0, '激活成功');
        } catch (\Exception $e) {
            $transaction->rollBack();
            // var_dump($e->getMessage());
            return $this->respondJson(1, $e->getMessage());
        }
    }

    /**
     * 用户是否存在推荐关系
     *
     * @return void
     */
    public function actionHasRecommend()
    {
        $userModel = $this->user;
        return $this->respondJson(0, '获取成功', !is_null($userModel->nodeRecommend));
    }
}
