<?php
namespace admin\controllers;

use common\services\AclService;
use common\services\UserService;
use common\services\VoteService;
use common\services\NodeService;
use common\services\RechargeService;
use yii\helpers\ArrayHelper;
use common\models\business\BUser;
use common\models\business\BNode;
use common\models\business\BVote;
use common\models\business\BNotice;
use common\models\business\BNodeType;
use common\models\business\BNodeRule;
use common\models\business\BUserIdentify;
use common\models\business\BTypeRuleContrast;
use common\models\business\BUserWallet;
use common\models\business\BUserCurrency;
use common\models\business\BVoucher;
use common\models\business\BUserCurrencyDetail;
use common\models\business\BVoucherDetail;
use common\models\business\BUserCurrencyFrozen;
use common\models\business\BUserRecommend;
use common\models\business\BSetting;
use common\models\business\BCurrency;
use common\models\business\BHistory;
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
            'download'
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
        $page = $this->pInt('page', 0);
        $order = $this->pString('order');
        if ($order != '') {
            $order_arr = [1 => 'A.create_time'];
            $order = $order_arr[$order];
        } else {
            $order = 'sum(C.vote_number)';
        }
        $data = NodeService::getList($page, $searchName, $str_time, $end_time, $type, 0, $order);
        $id_arr = [];
        foreach ($data as $v) {
            $id_arr[] = $v['id'];
        }
        $people = NodeService::getPeopleNum($id_arr, $str_time, $end_time);
        foreach ($data as &$v) {
            if (isset($people[$v['id']])) {
                $v['count'] = $people[$v['id']];
            } else {
                $v['count'] = 0;
            }
            
            $v['create_time'] = $v['create_time'] == 0 ? '-' :date('Y-m-d H:i:s', $v['create_time']);
            $v['examine_time'] = $v['examine_time'] == 0 ? '-' :date('Y-m-d H:i:s', $v['examine_time']);
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
            $order_arr = [1 => 'A.create_time'];
            $order = $order_arr[$order];
        } else {
            $order = 'A.create_time';
        }
        $data = NodeService::getList(0, $searchName, $str_time, $end_time, $type, 0, $order);
        $id_arr = [];
        foreach ($data as $v) {
            $id_arr[] = $v['id'];
        }
        $people = NodeService::getPeopleNum($id_arr, $str_time, $end_time);
        foreach ($data as $key => &$v) {
            if (isset($people[$v['id']])) {
                $v['count'] = $people[$v['id']];
            } else {
                $v['count'] = '0';
            }
            $v['key'] = $key+1;
            $v['create_time'] = $v['create_time'] == 0 ? '-' :date('Y-m-d H:i:s', $v['create_time']);
            $v['status'] = BNode::getStatus($v['status']);
        }
        $headers = ['key'=> '排名', 'name' => '节点名称', 'vote_number' => '票数', 'count' => '支持人数', 'grt' => '质押GRT', 'bpt' => '质押BPT', 'tt' => '质押TT', 'create_time' => '加入时间', 'status' => '状态'];
        $this->download($data, $headers, '节点列表'.date('YmdHis'));
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
        $page = $this->pInt('page', 0);
        $order = $this->pString('order');
        if ($order != '') {
            $order_arr = [1 => 'A.create_time'];
            $order = $order_arr[$order];
        } else {
            $order = 'A.create_time';
        }
        $data = NodeService::getList($page, $searchName, $str_time, $end_time, 0, $status, $order);
        $return = [];
        foreach ($data as $v) {
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
            $return[] = $item;
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
        $data->status = BNode::STATUS_ON;
        $data->status_remark = '已开启';
        if (!$data->save()) {
            return $this->respondJson(1, '审核失败', $data->getFirstErrorText());
        }
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
        $return['scheme'] = $data->scheme;
        $return['logo'] = FuncHelper::getImageUrl($data->logo, 640, 640);
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
        $page = $this->pInt('page', 0);
        $history = BHistory::find()->where(['<=', 'create_time', strtotime($endTime)])->orderBy('create_time DESC')->one();
        if (empty($history)) {
            return $this->respondJson(0, '获取成功', []);
        }
        $find = BHistory::find()->where(['update_number' => $history->update_number, 'node_type' => $type]);
        if ($page != 0) {
            $find->page($page);
        }
        $find->orderBy('vote_number DESC');
        $data = $find->asArray()->all();
        foreach ($data as &$v) {
            $v['count'] = $v['people_number'];
            $v['is_tenure'] = BNode::getTenure($v['is_tenure']);
        }
        return $this->respondJson(0, '获取成功', $data);
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
        $grt = $this->pInt('grt');
        if (empty($grt)) {
            return $this->respondJson(1, '质押grt必须大于0');
        }
        $tt = $this->pInt('tt');
        if (empty($tt)) {
            return $this->respondJson(1, '质押tt必须大于0');
        }
        $bpt = $this->pInt('bpt');
        if (empty($bpt)) {
            return $this->respondJson(1, '质押bpt必须大于0');
        }
        $node->is_examine = $is_examine;
        $node->is_candidate = $is_candidate;
        $node->is_vote = $is_vote;
        $node->is_order = $is_order;
        $node->tenure_num = $tenure_num;
        $node->max_candidate = $max_candidate;
        $node->grt = $grt;
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
        $is_tenure = $this->pInt('is_tenure', '');
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
        $grt = $this->pInt('grt');
        if (empty($grt)) {
            return $this->respondJson(1, '质压GRT数量不能为空');
        }
        $tt = $this->pInt('tt');
        if (empty($tt)) {
            return $this->respondJson(1, '质压TT数量不能为空');
        }
        $bpt = $this->pInt('bpt');
        if (empty($bpt)) {
            return $this->respondJson(1, '质压BPT数量不能为空');
        }
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
                return $this->respondJson(1, '注册失败', $user->getFirstErrorText());
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
        $setting = BNodeType::find()->where(['id' => $type_id])->one();
        if ($now_count >= $setting->max_candidate) {
            return $this->respondJson(1, '候选数量已达上限');
        }
        $node = new BNode();
        $node->user_id = $user->id;
        $node->type_id = $type_id;
        $node->is_tenure = $is_tenure;
        $node->grt = $grt;
        $node->tt = $tt;
        $node->bpt = $bpt;
        $node->status = BNode::STATUS_ON;
        $node->examine_time = time();

        if (!$node->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '注册失败', $node->getFirstErrorText());
        }
        $code = $this->pString('code');
        // 取出比例
        $setting = BSetting::find()->where(['key' => 'voucher_number'])->one();
        //判断是否已有推荐人
        $old_recommend = BUserRecommend::find()->where(['user_id' => $user->id])->one();
        if ($code != '' || $old_recommend != '') {
            if ($code != '') {
                $id = UserService::validateRemmendCode($code);

                if (empty($old_recommend)) {
                    $user_recommend = new BUserRecommend();
                    $user_recommend->user_id = $user->id;
                    $user_recommend->parent_id = $id;
                    $user_recommend->node_id = $node->id;
                    $user_recommend->amount = $grt * $setting->value;
                    if (!$user_recommend->save()) {
                        $transaction->rollBack();
                        return $this->respondJson(1, '注册失败', $user_recommend->getFirstErrorText());
                    }
                } elseif ($old_recommend->parent_id != $id) {
                    $transaction->rollBack();
                    return $this->respondJson(1, '此用户已有推荐人且与本次输出推荐码不一致', $node->getFirstErrorText());
                } else {
                    $old_recommend->node_id = $node->id;
                    $old_recommend->amount = $grt * $setting->value;
                    if (!$old_recommend->save()) {
                        $transaction->rollBack();
                        return $this->respondJson(1, '注册失败', $old_recommend->getFirstErrorText());
                    }
                }
            } else {
                $id = $old_recommend->parent_id;
                $old_recommend->node_id = $node->id;
                $old_recommend->amount = $grt * $setting->value;
                if (!$old_recommend->save()) {
                    $transaction->rollBack();
                    return $this->respondJson(1, '注册失败', $old_recommend->getFirstErrorText());
                }
            }

            $voucher = new BVoucher();
            $voucher->user_id = $id;
            $voucher->node_id = $node->id;
            $voucher->voucher_num = $grt * $setting->value;
            if (!$voucher->save()) {
                $transaction->rollBack();
                return $this->respondJson(1, '注册失败', $voucher->getFirstErrorText());
            }
            UserService::resetVoucher($id);
        }

        // 补全充值冻结信息
        $currency_arr = BCurrency::find()->all();
        $currency_id = [];
        foreach ($currency_arr  as $v) {
            $currency_id[$v['code']] = $v['id'];
        }
        //GRT
        $withdraw = new BUserRechargeWithdraw();
        $withdraw ->currency_id = $currency_id['grt'];
        $withdraw ->user_id = $user->id;
        $withdraw ->type = BUserRechargeWithdraw::$TYPE_RECHARGE;
        $withdraw ->amount = $grt;
        $withdraw ->transaction_id = (string)$node->id;
        $withdraw ->order_number = FuncHelper::generateOrderCode();
        $withdraw ->status = BUserRechargeWithdraw::$STATUS_EFFECT_SUCCESS;
        $withdraw ->remark = "添加节点充币";
        $withdraw ->audit_time = time();
        if (!$withdraw->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '注册失败', $withdraw->getFirstErrorText());
        }
        $userRechargeWithdrawId = $withdraw->id;

        $user_c_detail = new BUserCurrencyDetail();
        $user_c_detail->user_id = $user->id;
        $user_c_detail->currency_id = $currency_id['grt'];
        $user_c_detail->type = BUserCurrencyDetail::$TYPE_RECHARGE;
        $user_c_detail->amount = $grt;
        $user_c_detail->remark = '充币';
        $user_c_detail->status = BUserCurrencyDetail::$STATUS_EFFECT_SUCCESS;
        $user_c_detail->relate_table = 'user_recharge_withdraw';
        $user_c_detail->relate_id = $userRechargeWithdrawId;
        $user_c_detail->effect_time = time();
        if (!$user_c_detail->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '注册失败', $user_c_detail->getFirstErrorText());
        }

        $frozen = new BUserCurrencyFrozen();
        $frozen->user_id = $user->id;
        $frozen->currency_id = $currency_id['grt'];
        $frozen->amount = $grt;
        $frozen->remark = '节点竞选';
        $frozen->status = BUserCurrencyFrozen::STATUS_FROZEN;
        $frozen->type = BUserCurrencyFrozen::$TYPE_ELECTION;
        $frozen->relate_table = 'node';
        $frozen->relate_id = $node->id;
        if (!$frozen->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '注册失败', $frozen->getFirstErrorText());
        }

        UserService::resetCurrency($user->id, $currency_id['grt']);

        //TT
        $withdraw = new BUserRechargeWithdraw();
        $withdraw ->currency_id = $currency_id['tt'];
        $withdraw ->user_id = $user->id;
        $withdraw ->type = BUserRechargeWithdraw::$TYPE_RECHARGE;
        $withdraw ->amount = $tt;
        $withdraw ->transaction_id = (string)$node->id;
        $withdraw ->order_number = FuncHelper::generateOrderCode();
        $withdraw ->status = BUserRechargeWithdraw::$STATUS_EFFECT_SUCCESS;
        $withdraw ->remark = "添加节点充币";
        $withdraw ->audit_time = time();
        if (!$withdraw->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '注册失败', $withdraw->getFirstErrorText());
        }
        $userRechargeWithdrawId = $withdraw->id;

        $user_c_detail = new BUserCurrencyDetail();
        $user_c_detail->user_id = $user->id;
        $user_c_detail->currency_id = $currency_id['tt'];
        $user_c_detail->type = BUserCurrencyDetail::$TYPE_RECHARGE;
        $user_c_detail->amount = $tt;
        $user_c_detail->remark = '充币';
        $user_c_detail->status = BUserCurrencyDetail::$STATUS_EFFECT_SUCCESS;
        $user_c_detail->relate_table = 'user_recharge_withdraw';
        $user_c_detail->relate_id = $userRechargeWithdrawId;
        $user_c_detail->effect_time = time();
        if (!$user_c_detail->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '注册失败', $user_c_detail->getFirstErrorText());
        }

        $frozen = new BUserCurrencyFrozen();
        $frozen->user_id = $user->id;
        $frozen->currency_id = $currency_id['tt'];
        $frozen->amount = $tt;
        $frozen->remark = '节点竞选';
        $frozen->status = BUserCurrencyFrozen::STATUS_FROZEN;
        $frozen->type = BUserCurrencyFrozen::$TYPE_ELECTION;
        $frozen->relate_table = 'node';
        $frozen->relate_id = $node->id;
        if (!$frozen->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '注册失败', $frozen->getFirstErrorText());
        }

        UserService::resetCurrency($user->id, $currency_id['tt']);

        //BPT
        $withdraw = new BUserRechargeWithdraw();
        $withdraw ->currency_id = $currency_id['bpt'];
        $withdraw ->user_id = $user->id;
        $withdraw ->type = BUserRechargeWithdraw::$TYPE_RECHARGE;
        $withdraw ->amount = $bpt;
        $withdraw ->transaction_id = (string)$node->id;
        $withdraw ->order_number = FuncHelper::generateOrderCode();
        $withdraw ->status = BUserRechargeWithdraw::$STATUS_EFFECT_SUCCESS;
        $withdraw ->remark = "添加节点充币";
        $withdraw ->audit_time = time();
        if (!$withdraw->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '注册失败', $withdraw->getFirstErrorText());
        }
        $userRechargeWithdrawId = $withdraw->id;

        $user_c_detail = new BUserCurrencyDetail();
        $user_c_detail->user_id = $user->id;
        $user_c_detail->currency_id = $currency_id['bpt'];
        $user_c_detail->type = BUserCurrencyDetail::$TYPE_RECHARGE;
        $user_c_detail->amount = $bpt;
        $user_c_detail->remark = '充币';
        $user_c_detail->status = BUserCurrencyDetail::$STATUS_EFFECT_SUCCESS;
        $user_c_detail->relate_table = 'user_recharge_withdraw';
        $user_c_detail->relate_id = $userRechargeWithdrawId;
        $user_c_detail->effect_time = time();
        if (!$user_c_detail->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '注册失败', $user_c_detail->getFirstErrorText());
        }

        $frozen = new BUserCurrencyFrozen();
        $frozen->user_id = $user->id;
        $frozen->currency_id = $currency_id['bpt'];
        $frozen->amount = $bpt;
        $frozen->remark = '节点竞选';
        $frozen->status = BUserCurrencyFrozen::STATUS_FROZEN;
        $frozen->type = BUserCurrencyFrozen::$TYPE_ELECTION;
        $frozen->relate_table = 'node';
        $frozen->relate_id = $node->id;
        if (!$frozen->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '注册失败', $frozen->getFirstErrorText());
        }
        
        UserService::resetCurrency($user->id, $currency_id['bpt']);

        
        //     return $this->respondJson(0, '注册成功', ['user_id' => $user->id, 'is_identify' => $identify]);
        // }


        // public function actionSetIdentify()
        // {
        $user_id = $user->id;
        // $user_id = $this->pInt('user_id');
        // if (empty($user_id)) {
        //     return $this->respondJson(1, '用户ID不能为空');
        // }
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
        $data = BNode::find()->where(['user_id' => $user_id])->one();
        if (empty($data)) {
            return $this->respondJson(1, '此用户还没有节点');
        }
        $data->logo = $logo;
        $data->name = $name;
        $data->desc = $desc;
        $data->scheme = $scheme;
        $str = '添加';
        if (!$data->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '提交失败', $identify->getFirstErrorText());
        }

        $transaction->commit();
        return $this->respondJson(0, $str.'成功');
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
