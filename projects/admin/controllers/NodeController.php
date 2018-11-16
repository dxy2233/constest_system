<?php
namespace admin\controllers;

use common\services\AclService;
use common\services\UserService;
use common\services\VoteService;
use common\services\NodeService;
use common\services\SmsService;
use common\services\RechargeService;
use common\services\VoucherService;
use yii\helpers\ArrayHelper;
use common\models\business\BUser;
use common\models\business\BArea;
use common\models\business\BNode;
use common\models\business\BVote;
use common\models\business\BNotice;
use common\models\business\BNodeType;
use common\models\business\BUserOther;
use common\models\business\BNodeRule;
use common\models\business\BUserIdentify;
use common\models\business\BTypeRuleContrast;
use common\models\business\BUserWallet;
use common\models\business\BUserCurrency;
use common\models\business\BVoucher;
use common\models\business\BUserCurrencyDetail;
use common\models\business\BVoucherDetail;
use common\models\business\BSmsTemplate;
use common\models\business\BUserCurrencyFrozen;
use common\models\business\BUserRecommend;
use common\models\business\BSetting;
use common\models\business\BCurrency;
use common\models\business\BHistory;
use common\models\business\BCycle;
use common\models\business\BUserRechargeWithdraw;
use common\components\FuncHelper;

/**
 * Site controller
 */
class NodeController extends BaseController
{
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        
        $behaviors = [];
        $authActions = [
            'download',
            'history-download'
        ];

        if (isset($parentBehaviors['authenticator']['isThrowException'])) {
            if (!in_array(\Yii::$app->controller->action->id, $authActions)) {
                $parentBehaviors['authenticator']['isThrowException'] = true;
            }
        }

        return ArrayHelper::merge($parentBehaviors, $behaviors);
    }
    // 节点管理
    public function actionIndex()
    {
        // 节点类型
        $type = $this->pInt('type');
        $searchName = $this->pString('searchName', '');
        $str_time = $this->pString('str_time', '');
        $end_time = $this->pString('end_time', '');
        $page = $this->pInt('page', 1);
        $order = $this->pString('order');
        if ($order != '') {
            $order_arr = [1 => 'A.create_time', 2 => 'A.create_time DESC'];
            $order = $order_arr[$order];
        } else {
            $order = 'sum(C.vote_number) DESC,A.create_time ASC';
        }
        $data = NodeService::getIndexList($page, $searchName, $str_time, $end_time, $type, 0, $order);
        $id_arr = [];
        foreach ($data['list'] as $k => &$v) {
            $v['order'] = ($page-1)*20 + $k +1;
            $id_arr[] = $v['id'];
        }
        $people = NodeService::getPeopleNum($id_arr, $str_time, $end_time);
        foreach ($data['list'] as &$v) {
            if (isset($people[$v['id']])) {
                $v['count'] = $people[$v['id']];
            } else {
                $v['count'] = 0;
            }
            
            $v['create_time'] = $v['create_time'] == 0 ? '-' :date('Y-m-d H:i:s', $v['create_time']);
            $v['examine_time'] = $v['examine_time'] == 0 ? '-' :date('Y-m-d H:i:s', $v['examine_time']);
            $v['status'] = BNode::getStatus($v['status']);
        }
        return $this->respondJson(0, '获取成功', $data);
    }
    public function actionDownload()
    {
        // 节点类型
        $type = $this->gInt('type');
        $searchName = $this->gString('searchName', '');
        $str_time = $this->gString('str_time', '');
        $end_time = $this->gString('end_time', '');
        $order = $this->gString('order');
        if ($order != '') {
            $order_arr = [1 => 'A.create_time', 2 => 'A.create_time DESC'];
            $order = $order_arr[$order];
        } else {
            $order = 'sum(C.vote_number) DESC,A.create_time ASC';
        }
        $data = NodeService::getIndexList(0, $searchName, $str_time, $end_time, $type, 0, $order);
        $id_arr = [];

        foreach ($data['list'] as $v) {
            $id_arr[] = $v['id'];
        }
        $people = NodeService::getPeopleNum($id_arr, $str_time, $end_time);
        foreach ($data['list'] as $key => &$v) {
            if (isset($people[$v['id']])) {
                $v['count'] = $people[$v['id']];
            } else {
                $v['count'] = '0';
            }
            $v['key'] = $key+1;
            $v['create_time'] = $v['create_time'] == 0 ? '-' :date('Y-m-d H:i:s', $v['create_time']);
            $v['status'] = BNode::getStatus($v['status']);
            $v['is_tenure'] = $v['is_tenure'] == 1 ? '任职' : '候补';
        }

        $headers = ['key'=> '排名', 'name' => '节点名称', 'mobile' => '用户', 'vote_number' => '票数', 'count' => '支持人数', 'grt' => '质押GRT', 'bpt' => '质押BPT', 'tt' => '质押TT', 'is_tenure' => '身份', 'create_time' => '加入时间', 'status' => '状态'];
        $down = $this->download($data['list'], $headers, '节点列表'.date('YmdHis'));
        if (!$down) {
            exit('验证失败');
        }
        return;
    }
    // 审核列表
    public function actionExamine()
    {
        $status = $this->pInt('status', 2);
        if (empty($status)) {
            return $this->respondJson(1, '审核状态不能为空');
        }
        $searchName = $this->pString('searchName', '');
        $str_time = $this->pString('str_time', '');
        $end_time = $this->pString('end_time', '');
        $page = $this->pInt('page', 1);
        $order = $this->pString('order');
        if ($order != '') {
            $order_arr = [1 => 'A.create_time', 2 => 'A.create_time DESC'];
            $order = $order_arr[$order];
        } else {
            $order = 'A.create_time';
        }
        $data = NodeService::getIndexList($page, $searchName, $str_time, $end_time, 0, $status, $order);
        $return = [];
        $return['count'] = $data['count'];
        foreach ($data['list'] as $v) {
            $item = [];
            $item['id'] = $v['id'];
            $item['mobile'] = $v['mobile'];
            $item['name'] = $v['name'];
            $item['bpt'] = $v['bpt'];
            $item['tt'] = $v['tt'];
            $item['grt'] = $v['grt'];
            $item['type_name'] = $v['type_name'];
            $item['status'] = BNode::getStatus($v['status']);
            $item['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $item['examine_time'] = $v['examine_time'] == 0 ? '-' :date('Y-m-d H:i:s', $v['examine_time']);
            $return['list'][] = $item;
        }

        return $this->respondJson(0, '获取成功', $return);
    }


    // 审核通过
    public function actionExamineOn()
    {
        $nodeId = $this->pInt('nodeId');
        if (empty($nodeId)) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $data = BNode::find()->where(['id' => $nodeId])->one();
        if (empty($data)) {
            return $this->respondJson(1, '不存在的节点');
        }
        $now_count = BNode::find()->where(['type_id' => $data->type_id, 'status' => BNode::STATUS_ON])->count();
        $node_type = BNodeType::find()->where(['id' => $data->type_id])->one();
        if ($now_count >= $node_type->max_candidate) {
            return $this->respondJson(1, '候选数量已达上限');
        }
        $transaction = \Yii::$app->db->beginTransaction();
        // 赠送gdt
        $currencyDetail = new BUserCurrencyDetail();
        $currencyDetail->currency_id = BCurrency::getCurrencyIdByCode(BCurrency::$CURRENCY_GDT);
        $currencyDetail->status = BUserCurrencyDetail::$STATUS_EFFECT_SUCCESS;
        $currencyDetail->effect_time = NOW_TIME;
        $currencyDetail->remark = '申请节点奖励';
        $currencyDetail->user_id = $data->user_id;
        $currencyDetail->relate_table = 'node';
        $currencyDetail->type = BUserCurrencyDetail::$TYPE_REWARD;
        $currencyDetail->relate_id = $data->id;
        $currencyDetail->amount = $node_type->gdt_reward;
        if (!$currencyDetail->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '审核失败', $currencyDetail->getFirstErrorText());
        }

        //重算gdt
        UserService::resetCurrency($data->user_id, BCurrency::getCurrencyIdByCode(BCurrency::$CURRENCY_GDT));
        // 补全充值冻结信息
        $log = NodeService::addNodeMakeLogs($data);
        if ($log->code != 0) {
            $transaction->rollBack();
            return $this->respondJson(1, '注册失败'.$log->content);
        }

        $data->status = BNode::STATUS_ON;
        $data->examine_time = NOW_TIME;
        $data->status_remark = '已开启';
        if (!$data->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '审核失败', $data->getFirstErrorText());
        }
        // 发送短信通知用户
        $user = BUser::find()->where(['id' => $data->user_id])->one();
        $typeName = str_replace('节点', '', $node_type->name);
        $returnInfo = SmsService::send($user->mobile, ['name' => $typeName], BSmsTemplate::$TYPE_NODE_EXAMINE);
        if ($returnInfo->code != 0) {
            $transaction->rollBack();
            return $this->respondJson($returnInfo->code, $returnInfo->msg);
        }
        $transaction->commit();
        return $this->respondJson(0, '审核成功');
    }

    // 审核不通过
    public function actionExamineOff()
    {
        $nodeId = $this->pInt('nodeId');
        if (empty($nodeId)) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $remark = $this->pString('remark');
        if (empty($remark)) {
            return $this->respondJson(1, '原因不能为空');
        }
        $data = BNode::find()->where(['id' => $nodeId])->one();
        if (empty($data)) {
            return $this->respondJson(1, '不存在的节点');
        }
        $data->status = BNode::STATUS_NO;
        $data->status_remark = $remark;
        if (!$data->save()) {
            return $this->respondJson(1, '审核失败', $data->getFirstErrorText());
        }
        return $this->respondJson(0, '审核成功');
    }
    // 节点明细
    public function actionGetNodeData()
    {
        $nodeId = $this->pInt('nodeId');
        $data = BNode::find()->where(['id' => $nodeId])->one();
        if (empty($data)) {
            return $this->respondJson(1, '不存在的节点');
        }
        $return = [];
        $return['name'] = $data->name;
        $return['desc'] = $data->desc;
        $return['status_remark'] = $data->status_remark;
        
        if ($data->quota === null) {
            $return['quota'] = '';
        } else {
            $return['quota'] = $data->quota;
        }
        $return['scheme'] = $data->scheme;
        $return['logo'] = FuncHelper::getImageUrl($data->logo, 640, 640);
        return $this->respondJson(0, '获取成功', $return);
    }
    // 节点基本信息
    public function actionGetNodeUserData()
    {
        $nodeId = $this->pInt('nodeId');
        $data = BNode::find()->where(['id' => $nodeId])->one();
        if (empty($data)) {
            return $this->respondJson(1, '不存在的节点');
        }
        $node_type = BNodeType::find()->where(['id' => $data['type_id']])->one();
        $user = BUser::find()->where(['id' => $data->user_id])->one();
        $identify = BUserIdentify::find()->active()->where(['user_id' => $data->user_id])->one();
        $other = BUserOther::find()->where(['user_id' => $data->user_id])->one();
        $return = [];
        if ($other) {
            $return['weixin'] = $other->weixin;
            // $return['recommend_name'] = $other->recommend_name;
            // $return['recommend_mobile'] = $other->recommend_mobile;
            $return['grt_address'] = $other->grt_address;
            $return['tt_address'] = $other->tt_address;
            $return['bpt_address'] = $other->bpt_address;
        } else {
            $return['weixin'] =  $return['grt_address'] = $return['tt_address'] = $return['bpt_address'] = '';
        }
        
        if ($identify) {
            $return['username'] = $identify->realname;
        } else {
            $return['username'] = '';
        }
        $return['mobile'] = $user->mobile;
        $return['type_name'] = $node_type->name;
        $return['status_remark'] = $data->status_remark;

        $return['grt'] = $data->grt;
        $return['tt'] = $data->tt;
        $return['bpt'] = $data->bpt;
        return $this->respondJson(0, '获取成功', $return);
    }

    public function actionGetAddress()
    {
        $nodeId = $this->pInt('nodeId');
        if (empty($nodeId)) {
            return $this->respondJson(1, '节点ID不能为空');
        }
        $data = BNode::find()->where(['id' => $nodeId])->one();
        if (empty($data)) {
            return $this->respondJson(1, '不存在的节点');
        }
        $other = BUserOther::find()->where(['user_id' => $data->user_id])->one();
        if (empty($other)) {
            $return = [];
            $return['area_province_id'] =
            $return['area_city_id'] =
            $return['address'] =
            $return['zip_code'] =
            $return['consignee'] =
            $return['consignee_mobile'] = '';
            return $this->respondJson(0, '获取成功', $return);
        }
        $return = [];
        $return['area_province_id'] = BArea::getAreaOneName($other->area_province_id);
        $return['area_city_id'] = BArea::getAreaOneName($other->area_city_id);
        $return['address'] = $other->address;
        $return['zip_code'] = $other->zip_code;
        $return['consignee'] = $other->consignee;
        $return['consignee_mobile'] = $other->consignee_mobile;
        return $this->respondJson(0, '获取成功', $return);
    }
    
    // 获取节点实名信息
    public function actionGetNodeIdentify()
    {
        $nodeId = $this->pInt('nodeId');
        $data = BNode::find()->where(['id' => $nodeId])->one();
        if (empty($data)) {
            return $this->respondJson(1, '不存在的节点');
        }

        $identify = [];
        $userIdentify = BUserIdentify::find()->where(['user_id'=> $data->user_id])->orderBy('id DESC')->active(BNotice::STATUS_ACTIVE)->one();
        if (!empty($userIdentify)) {
            $identify['realName'] = $userIdentify->realname;
            $identify['number'] = $userIdentify->number;
            $identify['picFront'] = FuncHelper::getImageUrl($userIdentify->pic_front, 640, 640);
            $identify['picBack'] = FuncHelper::getImageUrl($userIdentify->pic_back, 640, 640);
        }
        return $this->respondJson(0, '获取成功', $identify);
    }

    // 获取投票明细
    public function actionGetVoteList()
    {
        $nodeId = $this->pInt('nodeId');
        $voteList = [];
        $data = BVote::find()
        ->from(BVote::tableName()." A")
        ->join('left join', BUser::tableName().' B', 'A.user_id = B.id')
        ->select(['A.*','B.mobile'])
        ->where(['A.node_id' => $nodeId, 'A.status' => BNotice::STATUS_ACTIVE])
        ->orderBy('A.create_time DESC')
        ->asArray()->all();
        foreach ($data as $v) {
            $voteItem = [];
            $voteItem['mobile'] = $v['mobile'];
            $voteItem['voteNumber'] = $v['vote_number'];
            $voteItem['createTime'] = date('Y-m-d H:i:s', $v['create_time']);
            $voteList[] = $voteItem;
        }
        $orderList = [];
        $order_data = BVote::find()
        ->from(BVote::tableName()." A")
        ->join('left join', BUser::tableName().' B', 'A.user_id = B.id')
        ->where(['A.node_id' => $nodeId, 'A.status' => BNotice::STATUS_ACTIVE])
        ->select(['sum(A.vote_number) as voteNumber','B.mobile'])
        ->groupBy(['A.user_id'])
        ->orderBy('sum(A.vote_number) desc')
        ->asArray()->all();
        foreach ($order_data as $v) {
            $voteItem = [];
            $voteItem['mobile'] = $v['mobile'];
            $voteItem['voteNumber'] = $v['voteNumber'];
            $orderList[] = $voteItem;
        }
        $return = [];
        $return['voteList'] = $voteList;
        $return['orderList'] = $orderList;
        return $this->respondJson(0, '获取成功', $return);
    }

    public function actionGetRule()
    {
        $nodeId = $this->pInt('nodeId');
        $data = BNode::find()->where(['id' => $nodeId])->one();
        if (empty($data)) {
            return $this->respondJson(1, '不存在的节点');
        }
        $order = NodeService::getNodeRanking($data->type_id, $nodeId);
        $rule_list = NodeService::getNodeRule($nodeId, $order);

        return $this->respondJson(0, '获取成功', $rule_list);
    }

    // 获取节点类型列表
    public function actionGetTypeList()
    {
        $data = BNodeType::find()->asArray()->all();
        return $this->respondJson(0, '获取成功', $data);
    }
    //历史排名
    public function actionGetHistoryOrder()
    {
        $type = $this->pInt('type');
        if (empty($type)) {
            return $this->respondJson(1, '节点类型不能为空');
        }
        $endTime = $this->pString('endTime', '');
        if ($endTime == '') {
            $endTime = date('Y-m-d H:i:s');
        }
        $page = $this->pInt('page', 1);
        $history = BHistory::find()->where(['<=', 'create_time', strtotime($endTime)])->orderBy('create_time DESC')->one();
        if (empty($history)) {
            return $this->respondJson(0, '获取成功', []);
        }
        $find = BHistory::find()->where(['update_number' => $history->update_number, 'node_type' => $type]);
        $count = $find->count();
        if ($page != 0) {
            $find->page($page);
        }
        $find->orderBy('vote_number DESC,create_time');
        $data = $find->asArray()->all();
        foreach ($data as $k => &$v) {
            $v['order'] = ($page-1)*20 + $k +1;
            $v['count'] = $v['people_number'];
            $v['is_tenure'] = BNode::getTenure($v['is_tenure']);
        }
        $return = [];
        $return['count'] = $count;
        $return['list'] = $data;
        return $this->respondJson(0, "获取成功", $return);
    }
    // 历史排名下载
    public function actionHistoryDownload()
    {
        $type = $this->gInt('type');
        if (empty($type)) {
            return $this->respondJson(1, '节点类型不能为空');
        }
        $endTime = $this->gString('endTime', '');
        if ($endTime == '') {
            $endTime = date('Y-m-d H:i:s');
        }
        $history = BHistory::find()->where(['<=', 'create_time', strtotime($endTime)])->orderBy('create_time DESC')->one();
        if (empty($history)) {
            return $this->respondJson(0, '获取成功', []);
        }
        $find = BHistory::find()->where(['update_number' => $history->update_number, 'node_type' => $type]);
        $find->orderBy('vote_number DESC,create_time');
        $data = $find->asArray()->all();
        foreach ($data as $k => &$v) {
            $v['order'] = $k +1;
            $v['count'] = $v['people_number'];
            $v['is_tenure'] = BNode::getTenure($v['is_tenure']);
        }
        $headers = ['order'=> '排名','node_name' => '节点名称', 'username' => '账号', 'vote_number' => '票数', 'count' => '支持人数', 'is_tenure' => '状态'];

        $down = $this->download($data, $headers, '历史排名'.date('YmdHis'));
        if (!$down) {
            exit('验证失败');
        }
        return;
    }
    // 获取节点设置
    public function actionGetNodeSetting()
    {
        $type_id = $this->pInt('type_id');
        if (empty($type_id)) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $data = BNodeType::find()->where(['id' => $type_id])->asArray()->one();
        
        if (!$data) {
            return $this->respondJson(1, '节点类型不存在');
        }
        $tenure = BNode::find()->where(['type_id' => $type_id])->select(['count(id) as allCount', 'sum(is_tenure) as allTenure'])->asArray()->one();
        if (empty($tenure)) {
            $data['allCount'] = $data['allTenure'] = 0;
        } else {
            $data['allTenure'] = $tenure['allTenure'];
            $data['allCount'] = $tenure['allCount'];
        }
        $rule = BTypeRuleContrast::find()->select(['min_order','max_order','rule_id'])->where(['type_id' => $type_id])->asArray()->all();
        
        $data['ruleList'] = $rule;
        return $this->respondJson(0, '获取成功', $data);
    }
    // 修改节点设置
    public function actionUpdate()
    {
        $id = $this->pInt('id');
        
        $name = $this->pString('name');
        if ($id) {
            $node = BNodeType::find()->where(['id' => $id])->one();
        } else {
            $node = new BNodeType();
            $node->name = $name;
        }
        if (empty($id) && empty($name)) {
            return $this->respondJson(1, 'ID与名称不能同时为空');
        }
        $is_examine = $this->pInt('isExamine', 0);
        
        $is_candidate = $this->pInt('isCandidate', 0);
        $is_vote = $this->pInt('isVote', 0);
        $is_order = $this->pInt('isOrder', 0);
        $tenure_num = $this->pInt('tenureNum');
        if (empty($tenure_num)) {
            return $this->respondJson(1, '任职数量必须大于0');
        }
        
        $max_candidate = $this->pInt('maxCandidate');
        if ($is_candidate > 0 && empty($max_candidate)) {
            return $this->respondJson(1, '候选数量必须大于0');
        }
        $tenure = BNode::find()->where(['type_id' => $id])->select(['count(id) as allCount', 'sum(is_tenure) as allTenure'])->asArray()->one();
        if ($tenure_num < $tenure['allTenure']) {
            return $this->respondJson(1, '任职数量必须大于当前任职数量');
        }
        if ($max_candidate < $tenure['allCount']) {
            return $this->respondJson(1, '候选数量必须大于当前候选数量');
        }
        $grt = $this->pInt('grt',0);

        $tt = $this->pInt('tt',0);

        $bpt = $this->pInt('bpt',0);

        $quota = $this->pInt('quota', 0);
        $gdt_reward = $this->pInt('gdtReward', 0);
        $node->is_examine = $is_examine;
        $node->gdt_reward = $gdt_reward;
        $node->is_candidate = $is_candidate;
        $node->is_vote = $is_vote;
        $node->is_order = $is_order;
        $node->tenure_num = $tenure_num;
        $node->max_candidate = $max_candidate;
        $node->grt = $grt;
        $node->quota = $quota;
        $node->tt = $tt;
        $node->bpt = $bpt;
        $transaction = \Yii::$app->db->beginTransaction();
        if (!$node->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '操作失败', $node->getFirstErrorText());
        }
        
        // $command = $node->createCommand();
        // var_dump($command->sql);
        // exit;
        $rule = $this->pString('rule', '');
        $rule_arr = json_decode($rule, true);
        // BTypeRuleContrast::find()->where(['type_id' => $node->id])->delete();
        \Yii::$app->db->createCommand()->delete(BTypeRuleContrast::tableName(), 'type_id = '.$node->id)->execute();
        foreach ($rule_arr as $v) {
            $rule_obj = new BTypeRuleContrast();
            $rule_obj->type_id = $node->id;
            $rule_obj->is_tenure = isset($v['is_tenure'])?$v['is_tenure']:$v['isTenure'];
            $rule_obj->min_order = isset($v['min_order'])?$v['min_order']:$v['minOrder'];
            $rule_obj->max_order = isset($v['max_order'])?$v['max_order']:$v['maxOrder'];
            $rule_obj->rule_id =isset($v['rule_id'])?$v['rule_id']:$v['ruleId'];
            if (!$rule_obj->save()) {
                $transaction->rollBack();
                return $this->respondJson(1, '操作失败', $rule_obj->getFirstErrorText());
            }
        }
        $transaction->commit();
        return $this->respondJson(0, '操作成功');
    }
    // 获取权益列表
    public function actionGetRuleList()
    {
        $data = BNodeRule::find()->asArray()->all();
        $return = [0 => [], 1 => [], 2 => []];
        foreach ($data as $v) {
            $return[$v['is_tenure']][] = $v;
        }
        return $this->respondJson(0, '获取成功', $return);
    }
    // 设置权益
    public function actionSetRule()
    {
        $data = json_decode($this->pString('data'), true);
        $old_data = BNodeRule::find()->all();
        $id_arr = [];
        foreach ($old_data  as $v) {
            $id_arr[$v['id']] = $v['id'];
        }
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($data as $v) {
            foreach ($v as $val) {
                if (isset($val['id'])) {
                    unset($id_arr[$val['id']]);
                    $node = BNodeRule::find()->where(['id' => $val['id']])->one();
                    if (empty($node)) {
                        $node = new BNodeRule();
                    }
                } else {
                    $node = new BNodeRule();
                }
                $node->name = $val['name'];
                $node->content = $val['content'];
                $node->is_tenure = $val['isTenure'];
                if (!$node->save()) {
                    $transaction->rollBack();
                    return $this->respondJson(1, '操作失败', $node->getFirstErrorText());
                }
            }
        }
        \Yii::$app->db->createCommand()->delete(BNodeRule::tableName(), ['in', 'id', $id_arr])->execute();
        $transaction->commit();
        return $this->respondJson(0, '操作成功');
    }
    // 任职
    public function actionTenure()
    {
        $nodeId = $this->pString('nodeId');
        $node = BNode::find()->where(['id' => $nodeId])->one();
        if (empty($node)) {
            return $this->respondJson(1, '不存在的节点');
        }
        $cycle = BCycle::find()->where(['>=','tenure_end_time',time()])->all();
        $bool = false;
        foreach ($cycle as $v) {
            if ($v->tenure_start_time <= time() && $v->tenure_end_time>=time()) {
                $bool = true;
            }
        }
        if (!$bool) {
            return $this->respondJson(1, '当前处于不可任职时间');
        }
        $now_count = BNode::find()->where(['type_id' => $node->type_id, 'is_tenure' => BNode::STATUS_ON, 'status' => BNode::STATUS_ON])->count();
        $setting = BNodeType::find()->where(['id' => $node->type_id])->one();
        if ($now_count >= $setting->tenure_num) {
            return $this->respondJson(1, '任职数量已达上限');
        }
        $node->is_tenure = BNotice::STATUS_ACTIVE;
        
        if (!$node->save()) {
            return $this->respondJson(1, '任职失败', $node->getFirstErrorText());
        }
    
        // 刷新节点投票排行
        NodeService::RefreshPushRanking($nodeId);
        return $this->respondJson(0, '任职成功');
    }
    // 取消任职
    public function actionUnTenure()
    {
        $nodeId = $this->pString('nodeId');
        $node = BNode::find()->where(['id' => $nodeId])->one();
        if (empty($node)) {
            return $this->respondJson(1, '不存在的节点');
        }
        $node->is_tenure = BNotice::STATUS_INACTIVE;

        if (!$node->save()) {
            return $this->respondJson(1, '撤职失败', $node->getFirstErrorText());
        }
    
        // 刷新节点投票排行
        NodeService::RefreshPushRanking($nodeId);
        return $this->respondJson(0, '撤职成功');
    }

    
    // 停用节点
    public function actionStopNode()
    {
        $nodeId = $this->pString('nodeId');
        $user_id = explode(',', $nodeId);
        $users = BNode::find()->where(['in','id',$user_id])->all();
        if (empty($users)) {
            return $this->respondJson(1, '不存在的节点');
        }
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($users as $user) {
            $user->status = BNotice::STATUS_INACTIVE;
            if (!$user->save()) {
                $transaction->rollBack();
                return $this->respondJson(1, '停用失败', $user->getFirstErrorText());
            }
        }
        $transaction->commit();
        return $this->respondJson(0, '停用成功');
    }

    // 启用节点
    public function actionOpenNode()
    {
        $nodeId = $this->pString('nodeId');
        $user_id = explode(',', $nodeId);
        $users = BNode::find()->where(['in','id',$user_id])->all();
        if (empty($users)) {
            return $this->respondJson(1, '不存在的节点');
        }
        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($users as $user) {
            $user->status = BNotice::STATUS_ACTIVE;
            if (!$user->save()) {
                $transaction->rollBack();
                return $this->respondJson(1, '启用失败', $user->getFirstErrorText());
            }
        }
        $transaction->commit();
        return $this->respondJson(0, '启用成功');
    }

    public function actionUpdateNode()
    {
        $nodeId = $this->pInt('nodeId');
        $data = BNode::find()->where(['id' => $nodeId])->one();
        if (empty($data)) {
            return $this->respondJson(1, '不存在的节点');
        }
        $logo = $this->pImage('logo', '');
        if (empty($logo)) {
            return $this->respondJson(1, 'logo不能为空');
        }
        $name = $this->pString('name', '');
        if (empty($name)) {
            return $this->respondJson(1, '名称不能为空');
        }
        $desc = $this->pString('desc', '');
        if (empty($desc)) {
            return $this->respondJson(1, '简介不能为空');
        }

        $scheme = $this->pString('scheme', '');
        if (empty($scheme)) {
            return $this->respondJson(1, '建设方案不能为空');
        }
        $is_tenure = $this->pInt('is_tenure', 0);
        $quota = \Yii::$app->request->post('quota', null);
        if ($quota !== '' && $quota !== null) {
            $data->quota = round(floatval($quota), 2);
        }
        $data->logo = $logo;
        $data->name = $name;
        $data->desc = $desc;
        $data->scheme = $scheme;
        $data->is_tenure = $is_tenure;
        $str = '编辑';
        if ($data->save()) {
            return $this->respondJson(0, $str.'成功');
        } else {
            return $this->respondJson(1, $str.'失败', $data->getFirstErrorText());
        }
    }

    public function actionCheckMobile()
    {
        $mobile = $this->pString('mobile');
        if (empty($mobile)) {
            return $this->respondJson(1, '手机不能为空');
        }
        if (!preg_match("/^1[345678]{1}\d{9}$/", $mobile)) {
            return $this->respondJson(1, '手机格式不正确');
        }
        $user = BUser::find()->where(['mobile' => $mobile])->one();
        //实名认证信息
        $identify = 0;
        if ($user) {
            $old_node = BNode::find()->where(['user_id' => $user->id])->one();
            if ($old_node) {
                return $this->respondJson(1, '此用户已有节点');
            }
            $user_identify = BUserIdentify::find()->where(['user_id' => $user->id])->one();
            if (!is_null($user_identify) && $user_identify->status == BUserIdentify::STATUS_ACTIVE) {
                $identify = 1;
            }
        }
        return $this->respondJson(0, '验证成功', ['is_identify' => $identify]);
    }

    //
    public function actionCheckNode()
    {
        $type_id = $this->pInt('type_id');
        if (empty($type_id)) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $is_tenure = $this->pInt('is_tenure');

        $now_count = BNode::find()->where(['type_id' => $type_id, 'status' => BNode::STATUS_ON])->count();
        $setting = BNodeType::find()->where(['id' => $type_id])->one();

        if ($now_count >= $setting->max_candidate) {
            return $this->respondJson(1, $setting->name.'候选数量已达上限');
        }
        if ($is_tenure == BNotice::STATUS_ACTIVE) {
            $now_count = BNode::find()->where(['type_id' => $type_id, 'is_tenure' => BNode::STATUS_ON, 'status' => BNode::STATUS_ON])->count();
            $setting = BNodeType::find()->where(['id' => $type_id])->one();
            if ($now_count >= $setting->tenure_num) {
                return $this->respondJson(1, $setting->name.'任职数量已达上限');
            }
        }
        return $this->respondJson(0, '验证成功');
    }
    // 添加节点
    public function actionCreateUser()
    {
        $mobile = $this->pString('mobile');
        if (empty($mobile)) {
            return $this->respondJson(1, '手机不能为空');
        }
        if (!preg_match("/^1[345678]{1}\d{9}$/", $mobile)) {
            return $this->respondJson(1, '手机格式不正确');
        }
        $type_id = $this->pInt('type_id');
        if (empty($type_id)) {
            return $this->respondJson(1, '节点类型不能为空');
        }
        $is_tenure = $this->pInt('is_tenure');
        if ($is_tenure == BNotice::STATUS_ACTIVE) {
            $now_count = BNode::find()->where(['type_id' => $type_id, 'is_tenure' => BNode::STATUS_ON, 'status' => BNode::STATUS_ON])->count();
            $setting = BNodeType::find()->where(['id' => $type_id])->one();
            if ($now_count >= $setting->tenure_num) {
                return $this->respondJson(1, '任职数量已达上限');
            }
        }
        $grt = $this->pInt('grt',0);

        $tt = $this->pInt('tt', 0);

        $bpt = $this->pInt('bpt', 0);

        $transaction = \Yii::$app->db->beginTransaction();
        $user = BUser::find()->where(['mobile' => $mobile])->one();
        //实名认证信息
        $identify = 0;
        if ($user) {
            $old_node = BNode::find()->where(['user_id' => $user->id])->one();
            if ($old_node) {
                return $this->respondJson(1, '此用户已有节点');
            }
            $user_identify = BUserIdentify::find()->where(['user_id' => $user->id])->andWhere(['status' => BUserIdentify::STATUS_ACTIVE])->one();
            if ($user_identify) {
                $identify = 1;
            }
        } else {
            $user = new BUser();
            $user->mobile = $mobile;
            $user->username = $mobile;
            $recommend_code = UserService::generateRemmendCode(6);
            $user->recommend_code = $recommend_code;
            if (!$user->save()) {
                $transaction->rollBack();
                return $this->respondJson(1, '注册失败'.$user->getFirstErrorText());
            }
            $currency = BCurrency::find()->where(['status' => BCurrency::$CURRENCY_STATUS_ON, 'recharge_status' => BCurrency::$RECHARGE_STATUS_ON])->all();
            foreach ($currency as $v) {
                $returnInfo = RechargeService::getAddress($v['id'], $user->id);
                
                if ($returnInfo->code) {
                    $transaction->rollBack();
                    return $this->respondJson(1, $returnInfo->msg);
                }
            }
        }
        $now_count = BNode::find()->where(['type_id' => $type_id, 'status' => BNode::STATUS_ON])->count();
        $node_type = BNodeType::find()->where(['id' => $type_id])->one();
        if ($now_count >= $node_type->max_candidate) {
            return $this->respondJson(1, '候选数量已达上限');
        }
        $node = new BNode();
        $node->user_id = $user->id;
        $node->type_id = $type_id;
        $node->is_tenure = $is_tenure;
        $node->grt = $grt;
        $node->tt = $tt;
        $node->bpt = $bpt;
        $logo = $this->pImage('logo', '');
        if (empty($logo)) {
            return $this->respondJson(1, 'logo不能为空');
        }
        $name = $this->pString('name', '');
        if (empty($name)) {
            return $this->respondJson(1, '名称不能为空');
        }
        $desc = $this->pString('desc', '');
        if (empty($desc)) {
            return $this->respondJson(1, '简介不能为空');
        }

        $scheme = $this->pString('scheme', '');
        if (empty($scheme)) {
            return $this->respondJson(1, '建设方案不能为空');
        }
        $node->logo = $logo;
        $node->name = $name;
        $node->desc = $desc;
        $node->scheme = $scheme;


        $node->status = BNode::STATUS_ON;
        $node->examine_time = time();

        if (!$node->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '注册失败'.$node->getFirstErrorText());
        }

        $weixin = $this->pString('weixin', '');
        // $recommend_mobile = $this->pString('recommend_mobile', '');
        // $recommend_name = $this->pString('recommend_name', '');
        $grt_address = $this->pString('grt_address', '');
        $tt_address = $this->pString('tt_address', '');
        $bpt_address = $this->pString('bpt_address', '');
        if ($bpt_address || $weixin || $grt_address || $tt_address) {
            // 添加个人其它信息
            $other = BUserOther::find()->where(['user_id' => $user->id])->one();
            if (empty($other)) {
                $other = new BUserOther();
                $other->user_id = $user->id;
            }
            $other->weixin = $weixin;
            // $other->recommend_mobile = $recommend_mobile;
            // $other->recommend_name = $recommend_name;
            $other->grt_address = $grt_address;
            $other->tt_address = $tt_address;
            $other->scenario = BUserOther::SCENARIO_APPLY;
            $other->bpt_address = $bpt_address;
            if (!$other->save()) {
                $transaction->rollBack();
                return $this->respondJson(1, '注册失败'.$other->getFirstErrorText());
            }
        }
        $code = $this->pString('code');
        // 取出比例
        $setting = BSetting::find()->where(['key' => 'voucher_number'])->one();
        //判断是否已有推荐人
        $old_recommend = BUserRecommend::find()->where(['user_id' => $user->id])->one();
        if ($code != '' || $old_recommend != '') {
            $setting_recommend_voucher = BSetting::find()->where(['key' => 'recommend_voucher'])->one();
            if ($code != '') {
                $id = UserService::validateRemmendCode($code);
                $parent_node = BNode::find()->active()->where(['user_id' => $id])->one();
                if (empty($old_recommend)) {// 推荐关系为空时具有推荐码添加新推荐数据
                    $user_recommend = new BUserRecommend();
                    $user_recommend->user_id = $user->id;
                    $user_recommend->parent_id = $id;
                    $user_recommend->node_id = $node->id;
                    if (!empty($parent_node) && $setting_recommend_voucher->value == 1) { // 推荐人是节点送券
                        $user_recommend->amount = $grt * $setting->value;
                        UserService::resetVoucher($id);
                        $res = VoucherService::createNewVoucher($id, $node->id, $grt * $setting->value);
                        if ($res->code != 0) {
                            $transaction->rollBack();
                            return $this->respondJson(1, '注册失败'.$res->msg());
                        }
                    }
                    if (!$user_recommend->save()) {
                        $transaction->rollBack();
                        return $this->respondJson(1, '注册失败'.$user_recommend->getFirstErrorText());
                    }
                } elseif ($old_recommend->parent_id != $id) {
                    $transaction->rollBack();
                    return $this->respondJson(1, '此用户已有推荐人且与本次输出推荐码不一致');
                } elseif (!empty($parent_node) && $setting_recommend_voucher->value == 1) { // 有推荐关系且推荐人是节点直接送券
                    $old_recommend->node_id = $node->id;
                    $old_recommend->amount = $grt * $setting->value;
                    if (!$old_recommend->save()) {
                        $transaction->rollBack();
                        return $this->respondJson(1, '注册失败'.$old_recommend->getFirstErrorText());
                    }

                    $res = VoucherService::createNewVoucher($id, $node->id, $grt * $setting->value);
                    if ($res->code != 0) {
                        $transaction->rollBack();
                        return $this->respondJson(1, '注册失败'.$res->msg());
                    }
                } else {// 有推荐关系且推荐人不是节点只修改对应node关系
                    $old_recommend->node_id = $node->id;
                    if (!$old_recommend->save()) {
                        $transaction->rollBack();
                        return $this->respondJson(1, '注册失败'.$old_recommend->getFirstErrorText());
                    }
                }
            } else { // code为空时必定有推荐关系
                $id = $old_recommend->parent_id;
                $parent_node = BNode::find()->active()->where(['user_id' => $id])->one();
                $old_recommend->node_id = $node->id;
                if (!empty($parent_node) && $setting_recommend_voucher->value == 1) { //推荐人是节点直接送券
                    
                    $old_recommend->amount = $grt * $setting->value;
                    if (!$old_recommend->save()) {
                        $transaction->rollBack();
                        return $this->respondJson(1, '注册失败'.$old_recommend->getFirstErrorText());
                    }

                    $res = VoucherService::createNewVoucher($id, $node->id, $grt * $setting->value);
                    if ($res->code != 0) {
                        $transaction->rollBack();
                        return $this->respondJson(1, '注册失败'.$res->msg());
                    }
                } else { // 其它情况只修改node 对应关系
                    if (!$old_recommend->save()) {
                        $transaction->rollBack();
                        return $this->respondJson(1, '注册失败'.$old_recommend->getFirstErrorText());
                    }
                }
            }
        }
        // 补全充值冻结信息
        $log = NodeService::addNodeMakeLogs($node);
        if ($log->code != 0) {
            $transaction->rollBack();
            return $this->respondJson(1, '注册失败'.$log->content);
        }
        // 赠送gdt
        $currencyDetail = new BUserCurrencyDetail();
        $currencyDetail->currency_id = BCurrency::getCurrencyIdByCode(BCurrency::$CURRENCY_GDT);
        $currencyDetail->status = BUserCurrencyDetail::$STATUS_EFFECT_SUCCESS;
        $currencyDetail->effect_time = NOW_TIME;
        $currencyDetail->remark = '申请节点奖励';
        $currencyDetail->user_id = $user->id;
        $currencyDetail->relate_table = 'node';
        $currencyDetail->type = BUserCurrencyDetail::$TYPE_REWARD;
        $currencyDetail->relate_id = $node->id;
        $currencyDetail->amount = $node_type->gdt_reward;

        if (!$currencyDetail->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '注册失败'.$currencyDetail->getFirstErrorText());
        }
        //重算gdt
        UserService::resetCurrency($user->id, BCurrency::getCurrencyIdByCode(BCurrency::$CURRENCY_GDT));
        // 实名认证信息
        $user_id = $user->id;
        
        if (!$identify) {
            $user_identify = BUserIdentify::find()->where(['user_id' => $user->id])->andWhere(['status' => BUserIdentify::STATUS_INACTIVE])->one();
            if ($user_identify) {
                $user_identify->delete();
            }
            $realname = $this->pString('realname');
            if (empty($realname)) {
                return $this->respondJson(1, '用户姓名不能为空');
            }
            $number = $this->pString('identify');
            if (empty($number)) {
                return $this->respondJson(1, '身份证号不能为空');
            }
            $pic_front = $this->pImage('pic_front');
            if (empty($pic_front)) {
                return $this->respondJson(1, '证件图片正面不能为空');
            }
            $pic_back = $this->pImage('pic_back');
            if (empty($pic_back)) {
                return $this->respondJson(1, '证件图片背面不能为空');
            }
            $identify = new BUserIdentify();
            $identify->user_id = $user_id;
            $identify->realname = $realname;
            $identify->number = (string)$number;
            $identify->pic_front = $pic_front;
            $identify->examine_time = time();
            $identify->audit_admin_id = $this->user_id;
            $identify->pic_back = $pic_back;
            $identify->type = 1;
            $identify->status = BNotice::STATUS_ACTIVE;
            if (!$identify->save()) {
                $transaction->rollBack();
                return $this->respondJson(1, '实名信息添加失败', $identify->getFirstErrorText());
            }
        }
        //     return $this->respondJson(0, '提交成功', ['user_id' => $user_id]);
        // }

        // public function actionCreateNode()
        // {
        // $user_id = $this->pInt('user_id');
        // if (empty($user_id)) {
        //     return $this->respondJson(1, '用户ID不能为空');
        // }


        $transaction->commit();
        return $this->respondJson(0, '添加成功');
    }


    // 删除记录
    public function actionDelOldData()
    {
        $nodeId = $this->pString('nodeId');
        $user_id = explode(',', $nodeId);
        $users = BNode::find()->where(['in','id',$user_id])->all();
        if (empty($users)) {
            return $this->respondJson(1, '不存在的节点');
        }
        $res = BNode::updateAll(['status' => BNode::STATUS_DEL], ['and', ['in', 'id', $user_id], ['status' => BNode::STATUS_NO]]);
        if ($res) {
            return $this->respondJson(0, '删除成功');
        } else {
            return $this->respondJson(0, '删除失败');
        }
    }
}
