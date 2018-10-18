<?php
namespace admin\controllers;

use common\services\AclService;
use common\services\NodeService;
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
        $data = NodeService::getList($page, $searchName, $str_time, $end_time, $type);
        $id_arr = [];
        foreach ($data as $v) {
            $id_arr[] = $v['id'];
        }
        $people = NodeService::getPeopleNum($id_arr, $str_time, $end_time);
        foreach ($data as &$v) {
            $v['count'] = $people[$v['id']];
        }
        return $this->respondJson(0, '获取成功', $data);
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
        $data = NodeService::getList($page, $searchName, $str_time, $end_time, 0, $status);
        $return = [];
        foreach ($data as $v) {
            $item = [];
            $item['username'] = $v['username'];
            $item['name'] = $v['name'];
            $item['consume'] = $v['consume'];
            $item['status'] = BNode::getStatus($v['status']);
            $item['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
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
        $return['scheme'] = $data->scheme;
        $return['logo'] = $data->logo;
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
        $userIdentify = BUserIdentify::find()->where(['user_id'=> $data->user_id])->active(BNotice::STATUS_ACTIVE)->one();
        if (!empty($userIdentify)) {
            $identify['realName'] = $userIdentify->realname;
            $identify['number'] = $userIdentify->number;
            $identify['picFront'] = $userIdentify->pic_front;
            $identify['picBack'] = $userIdentify->pic_back;
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
        ->asArray()->all();
        foreach ($data as $v) {
            $voteItem = [];
            $voteItem['mobile'] = $v['mobile'];
            $voteItem['voteNumber'] = $v['vote_number'];
            $voteItem['createTime'] = $v['create_time'];
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
        // $type_data = BNodeType::find()->where(['id' => $data->type_id])->one();
        // $rule_arr = json_decode($type_data->rule_list);
        // if (count($rule_arr) == 0) {
        //     return $this->respondJson(0, '获取成功', []);
        // }
        // $rule_data = BNodeRule::find()->where(['in','id',$rule_arr])->asArray()->all();
        return $this->respondJson(0, '获取成功', []);
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
        $page = $this->pInt('page', 1);
        $data = NodeService::getList($page, '', '', $endTime, $type);
        $id_arr = [];
        foreach ($data as $v) {
            $id_arr[] = $v['id'];
        }
        $people = NodeService::getPeopleNum($id_arr, '', $endTime);
        foreach ($data as &$v) {
            $v['count'] = $people[$v['id']];
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
        $min_money = $this->pInt('minMoney');
        if (empty($tenure_num)) {
            return $this->respondJson(1, '质押资产必须大于0');
        }
        $node->is_examine = $is_examine;
        $node->is_candidate = $is_candidate;
        $node->is_vote = $is_vote;
        $node->is_order = $is_order;
        $node->tenure_num = $tenure_num;
        $node->max_candidate = $max_candidate;
        $node->min_money = $min_money;
        $transaction = \Yii::$app->db->beginTransaction();
        if (!$node->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '操作失败', $node->getFirstErrorText());
        }
        $rule = $this->pString('rule', '');
        $rule_arr = json_decode($rule, true);
        // BTypeRuleContrast::find()->where(['type_id' => $node->id])->delete();
        \Yii::$app->db->createCommand()->delete(BTypeRuleContrast::tableName(), 'type_id = '.$node->id)->execute();
        foreach ($rule_arr as $v) {
            $rule_obj = new BTypeRuleContrast();
            $rule_obj->type_id = $node->id;
            $rule_obj->is_tenure = $v['isTenure'];
            $rule_obj->min_order = $v['minOrder'];
            $rule_obj->max_order = $v['maxOrder'];
            $rule_obj->rule_id = $v['ruleId'];
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
        $return = array('isTenure'=>array(),'noTenure'=>array());
        foreach ($data as $v) {
            if ($v['is_tenure'] == 1) {
                $return['isTenure'][] = $v;
            } else {
                $return['noTenure'][] = $v;
            }
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

    public function actionTenure()
    {
        $nodeId = $this->pString('nodeId');
        $node = BNode::find()->where(['id' => $nodeId])->one();
        if (empty($node)) {
            return $this->respondJson(1, '不存在的节点');
        }
        $node->is_tenure = BNotice::STATUS_ACTIVE;

        if (!$node->save()) {
            return $this->respondJson(1, '任职失败', $node->getFirstErrorText());
        }
    
        return $this->respondJson(0, '任职成功');
    }

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
        $logo = $this->pString('logo', '');
        $name = $this->pString('name', '');
        $desc = $this->pString('desc', '');
        $scheme = $this->pString('scheme', '');
        $data->logo = $logo;
        $data->name = $name;
        $data->desc = $desc;
        $data->scheme = $scheme;
        $str = '编辑';
        if ($data->save()) {
            return $this->respondJson(0, $str.'成功');
        } else {
            return $this->respondJson(1, $str.'失败', $data->getFirstErrorText());
        }
    }
}
