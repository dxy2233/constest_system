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
use common\models\business\BUnvote;
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

    public function actionIndex()
    {
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
        $type_data = BNodeType::find()->where(['id' => $data->type_id])->one();
        $rule_arr = json_decode($type_data->rule_list);
        if (count($rule_arr) == 0) {
            return $this->respondJson(0, '获取成功', []);
        }
        $rule_data = BNodeRule::find()->where(['in','id',$rule_arr])->asArray()->all();
        return $this->respondJson(0, '获取成功', $rule_data);
    }


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
        $is_examine = $this->pInt('is_examine', 0);
        
        $is_candidate = $this->pInt('is_candidate', 0);
        $is_vote = $this->pInt('is_vote', 0);
        $is_order = $this->pInt('is_order', 0);
        $tenure_num = $this->pInt('tenure_num');
        if (empty($tenure_num)) {
            return $this->respondJson(1, '任职数量必须大于0');
        }
        $max_candidate = $this->pInt('max_candidate');
        if ($is_candidate > 0 && empty($max_candidate)) {
            return $this->respondJson(1, '候选数量必须大于0');
        }
        $min_money = $this->pInt('min_money');
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
            $rule_obj->is_tenure = $v['is_tenure'];
            $rule_obj->min_order = $v['min_order'];
            $rule_obj->max_order = $v['max_order'];
            $rule_obj->rule_id = $v['rule_id'];
            if (!$rule_obj->save()) {
                $transaction->rollBack();
                return $this->respondJson(1, '操作失败', $rule_obj->getFirstErrorText());
            }
        }
        $transaction->commit();
        return $this->respondJson(0, '操作成功');
    }

    public function actionGetRuleList()
    {
        $data = BNodeRule::find()->asArray()->all();
        return $this->respondJson(0, '获取成功', $data);
    }

    public function actionSetRule()
    {
        $id = $this->pInt('id');
        if ($id) {
            $node = BNodeRule::find()->where(['id' => $id])->one();
        } else {
            $node = new BNodeRule();
        }
        $name = $this->pString('name');

        if (empty($name)) {
            return $this->respondJson(1, '权益名称不能为空');
        }
        $content = $this->pString('content');
        if (empty($name)) {
            return $this->respondJson(1, '权益描述不能为空');
        }
        $is_tenure = $this->pInt('is_tenure', 0);
        $node->name = $name;
        $node->content = $content;
        $node->is_tenure = $is_tenure;
        if (!$node->save()) {
            return $this->respondJson(1, '操作失败', $node->getFirstErrorText());
        } else {
            return $this->respondJson(0, '操作成功');
        }
    }
}
