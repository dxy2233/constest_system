<?php
namespace admin\controllers;

use common\services\AclService;
use common\services\TicketService;
use common\services\NodeService;
use yii\helpers\ArrayHelper;
use common\models\business\BUserOther;
use common\models\business\BArea;
use common\models\business\BUser;
use common\models\business\BUserIdentify;
use common\models\business\BNode;
use common\models\business\BNodeType;
use common\models\business\BNodeTransfer;
use common\models\business\BNodeRecommend;
use common\models\business\BUserLog;
use common\models\business\BVote;
use common\components\IpUtil;

/**
 * Site controller
 */
class TransferController extends BaseController
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

    // 获取节点转让基本信息
    public function actionGetNodeTransfer()
    {
        $id = $this->pInt('id');
        if (!$id) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $data = BNode::find()
        ->from(BNode::tableName()." A")
        ->select(['A.user_id as from_id', 'B.name as node_type', 'C.realname', 'C.number', 'D.mobile'])
        ->join('left join', BNodeType::tableName().' B', 'A.type_id = B.id')
        ->join('left join', BUser::tableName().' D', 'D.id = A.user_id')
        ->join('left join', BUserIdentify::tableName().' C', 'A.user_id = C.user_id && C.status = '.BUserIdentify::STATUS_ACTIVE)
        ->where(['a.id' => $id])
        ->asArray()->one();
        if (!$data) {
            return $this->respondJson(1, '不存在的节点');
        }
        return $this->respondJson(0, '获取成功', $data);
    }

    // 通过手机号获取用户姓名及身份证号
    public function actionGetUserNameAndIdentify()
    {
        $mobile = $this->pString('mobile');
        $user = BUser::find()->where(['mobile' => $mobile])->one();
        if (!$user) {
            return $this->respondJson(1, '不存在的用户');
        }
        $identify = BUserIdentify::find()->where(['user_id' => $user->id])->active()->one();
        if (!$identify) {
            return $this->respondJson(1, '此用户未实名认证');
        }
        $return = ['to_id' => $user->id, 'real_name' => $identify->realname, 'number' => $identify->number];
        return $this->respondJson(0, '获取成功', $return);
    }

    // 提交节点转让申请
    public function actionCreateTransfer()
    {
        $from_id = $this->pInt('fromId');
        if (!$from_id) {
            return $this->respondJson(1, '转让人ID不能为空');
        }
        $node = BNode::find()->where(['user_id' => $from_id])->active()->one();
        if (!$node) {
            return $this->respondJson(1, '转让人非节点用户');
        }
        $to_id = $this->pInt('toId');
        if (!$to_id) {
            return $this->respondJson(1, '受让人ID不能为空');
        }
        $to_user = BUser::find()->where(['id' => $to_id])->one();
        if (!$to_user) {
            return $this->respondJson(1, '受让人不存在');
        }
        $to_node = BNode::find()->where(['user_id' => $to_id])->active()->one();
        if ($to_node) {
            return $this->respondJson(1, '受让人已有节点');
        }
        $to_identify = BUserIdentify::find()->where(['user_id' => $to_id])->active()->one();
        if (!$to_identify) {
            return $this->respondJson(1, '受让人未实名认证');
        }
        $images = $this->pString('images');
        if (!$images) {
            return $this->respondJson(1, '申请凭证不能为空');
        }
        $old_data = BNodeTransfer::find()->where(['status' => BNodeTransfer::STATUS_INACTIVE, 'from_user_id' => $from_id])->one();
        
        if ($old_data) {
            return $this->respondJson(1, '已有其它未审核的转让申请');
        }
        $old_up = BNodeUpgrade::find()->where(['status' => BNodeUpgrade::STATUS_INACTIVE, 'user_id' => $from_id])->one();
        if ($old_up) {
            return $this->respondJson(1, '已有其它未审核的升级申请');
        }
        $transfer = new BNodeTransfer();
        $transfer->from_user_id = $from_id;
        $transfer->to_user_id = $to_id;
        $transfer->grt = $node->grt;
        $transfer->tt = $node->tt;
        $transfer->node_type = $node->type_id;
        $transfer->bpt = $node->bpt;
        $transfer->node_id = $node->id;
        $transfer->images = $images;
        $transfer->status = BNodeTransfer::STATUS_INACTIVE;
        $transfer->status_remark = BNodeTransfer::getStatus($transfer->status);
        $transfer->create_time = time();
        if ($transfer->save()) {
            return $this->respondJson(0, '提交成功');
        } else {
            return $this->respondJson(1, '提交失败', $transfer->getFirstErrorText());
        }
    }

    public function actionIndex()
    {
        $searchName = $this->pString('searchName');
        $find = BNodeTransfer::find()
        ->from(BNodeTransfer::tableName().' A')
        ->select(['A.id','B.name as node_name', 'C.name as type_name', 'D.mobile as from_user_mobile', 'E.realname as from_user_name', 'F.mobile as to_user_mobile', 'G.realname as to_user_name', 'A.status', 'A.create_time', 'A.examine_time'])
        ->join('left join', BNode::tableName().' B', 'A.node_id = B.id')
        ->join('left join', BNodeType::tableName().' C', 'B.type_id = C.id')
        ->join('left join', BUser::tableName().' D', 'A.from_user_id = D.id')
        ->join('left join', BUserIdentify::tableName().' E', 'A.from_user_id = E.user_id && E.status = '.BUserIdentify::STATUS_ACTIVE)
        ->join('left join', BUser::tableName().' F', 'A.to_user_id = F.id')
        ->join('left join', BUserIdentify::tableName().' G', 'A.to_user_id = G.user_id && G.status = '.BUserIdentify::STATUS_ACTIVE);
        if ($searchName != '') {
            $find->andWhere(['or', ['like', 'B.name', $searchName], ['like', 'D.mobile', $searchName]]);
        }
        $page = $this->pInt('page', 1);
        $status = $this->pInt('status', 0);
        $find->andWhere(['A.status' => $status]);
        $count = $find->count();
        $order = $this->pString('order');
        if ($order != '') {
            $order_arr = [1 => 'A.create_time', 2 => 'A.create_time desc'];
            $order = $order_arr[$order];
        } else {
            $order = 'A.create_time desc';
        }
        
        $find->orderBy($order);
        $find->page($page);
        $data = $find->asArray()->all();
        foreach ($data as &$v) {
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $v['examine_time'] = date('Y-m-d H:i:s', $v['examine_time']);
            $v['status'] = BNodeTransfer::getStatus($v['status']);
        }
        $return = ['list' => $data, 'count' => $count];
        return $this->respondJson(0, '获取成功', $return);
    }

    //转让审核明细
    public function actionGetTransferDetail()
    {
        $id = $this->pInt('id');
        if (!$id) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $data = BNodeTransfer::find()->where(['id' => $id])->one();
        
        if (!$data) {
            return $this->respondJson(1, '数据不存在');
        }
        $return = [];
        
        $node_type = BNodeType::find()->where(['id' => $data->node_type])->one();
        $return['node_type'] = $node_type->name;
        $old_user = BUser::find()->where(['id' => $data->from_user_id])->one();
        $return['from_user_mobile'] = $old_user->mobile;
        $old_identify = BUserIdentify::find()->where(['user_id' => $data->from_user_id])->active()->one();
        $return['from_user_name'] = $old_identify->realname;
        $return['from_user_number'] = $old_identify->number;
        $new_user = BUser::find()->where(['id' => $data->to_user_id])->one();
        $return['to_user_mobile'] = $new_user->mobile;
        $new_identify = BUserIdentify::find()->where(['user_id' => $data->to_user_id])->active()->one();
        $return['to_user_name'] = $new_identify->realname;
        $return['to_user_number'] = $new_identify->number;
        $return['images'] = $data->images;
        return $this->respondJson(0, '获取成功', $return);
    }

    public function actionDownload()
    {
        $down = $this->checkDownloadCode();
        if (!$down) {
            exit('验证失败');
        }
        $searchName = $this->gString('searchName');
        $find = BNodeTransfer::find()
        ->from(BNodeTransfer::tableName().' A')
        ->select(['A.id','B.name as node_name', 'C.name as type_name', 'D.mobile as from_user_mobile', 'E.realname as from_user_name', 'F.mobile as to_user_mobile', 'G.realname as to_user_name', 'A.status', 'A.create_time', 'A.examine_time'])
        ->join('left join', BNode::tableName().' B', 'A.node_id = B.id')
        ->join('left join', BNodeType::tableName().' C', 'B.type_id = C.id')
        ->join('left join', BUser::tableName().' D', 'A.from_user_id = D.id')
        ->join('left join', BUserIdentify::tableName().' E', 'A.from_user_id = E.user_id && E.status = '.BUserIdentify::STATUS_ACTIVE)
        ->join('left join', BUser::tableName().' F', 'A.to_user_id = F.id')
        ->join('left join', BUserIdentify::tableName().' G', 'A.to_user_id = G.user_id && G.status = '.BUserIdentify::STATUS_ACTIVE);
        if ($searchName != '') {
            $find->andWhere(['or', ['like', 'B.name', $searchName], ['like', 'D.mobile', $searchName]]);
        }
        $page = $this->gInt('page', 1);
        $status = $this->gInt('status', 0);
        $find->andWhere(['A.status' => $status]);
        $order = $this->gString('order');
        if ($order != '') {
            $order_arr = [1 => 'A.create_time', 2 => 'A.create_time desc'];
            $order = $order_arr[$order];
        } else {
            $order = 'A.create_time desc';
        }
        $find->orderBy($order);
        $data = $find->asArray()->all();
        foreach ($data as &$v) {
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $v['examine_time'] = date('Y-m-d H:i:s', $v['examine_time']);
            $v['status'] = BNodeTransfer::getStatus($v['status']);
        }
        $return = ['list' => $data];
        $return['list'] = $data;
        $headers = ['node_name'=> '转让节点名称', 'type_name' => '转让节点类型', 'from_user_mobile' => '转让方手机号', 'from_user_name' => '转让方姓名', 'to_user_mobile' => '受让方手机号', 'to_user_name' => '受让方姓名', 'status' => '状态', 'create_time' => '提交时间', 'examine_time' => '审核时间'];
        $this->download($return['list'], $headers, '节点转让'.date('YmdHis'));

        return;
    }

    // 节点转让审核通过
    public function actionExamineOn()
    {
        $id = $this->pInt('id');
        if (!$id) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $data = BNodeTransfer::find()->where(['id' => $id])->one();
        if (!$data) {
            return $this->respondJson(1, '数据不存在');
        }
        $node = BNode::find()->where(['user_id' => $data->from_user_id])->active()->one();
        if (!$node) {
            return $this->respondJson(1, '转让人非节点用户');
        }

        $to_user = BUser::find()->where(['id' => $data->to_user_id])->one();
        if (!$to_user) {
            return $this->respondJson(1, '受让人不存在');
        }
        $to_node = BNode::find()->where(['user_id' => $data->to_user_id])->active()->one();
        if ($to_node) {
            return $this->respondJson(1, '受让人已有节点');
        }
        $to_identify = BUserIdentify::find()->where(['user_id' => $data->to_user_id])->active()->one();
        if (!$to_identify) {
            return $this->respondJson(1, '受让人未实名认证');
        }
        // 修改审核状态
        $transaction = \Yii::$app->db->beginTransaction();
        $data->status = BNodeTransfer::STATUS_ACTIVE;
        $data->examine_time = time();
        $data->examine_user = $this->user_id;
        if (!$data->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '审核失败', $data->getFirstErrorText());
        }
        // 修改节点对应user_id
        $node->user_id = $data->to_user_id;
        if (!$node->save()) {
            $transaction->rollBack();
            return $this->respondJson(1, '审核失败', $node->getFirstErrorText());
        }
        $node_recommend = BNodeRecommend::find()->where(['user_id' => $data->from_user_id])->one();
        if ($node_recommend) {
            $sign = BNodeRecommend::updateAll(
                ['user_id' => $data->to_user_id],
                ['=', 'user_id', $data->from_user_id]
            );
            if (!$sign) {
                $transaction->rollBack();
                return $this->respondJson(1, '审核失败', '节点推荐关系修改失败');
            }
            $str = $node_recommend->parent_list. ','. $data->from_user_id;
            $new_str = $node_recommend->parent_list. ','. $data->to_user_id;
        } else {
            $str = $data->from_user_id;
            $new_str = $data->to_user_id;
        }
        $other_recommend = BNodeRecommend::find()->where(['parent_id' => $data->from_user_id])->one();
        if ($other_recommend) {
            $sign = BNodeRecommend::updateAll(
                ['parent_id' => $data->to_user_id],
                ['=', 'parent_id', $data->from_user_id]
            );
            if (!$sign) {
                $transaction->rollBack();
                return $this->respondJson(1, '审核失败', '下级节点推荐关系修改失败');
            }
        }

        
        $sql = "UPDATE `gr_contest`.`gr_user_recommend` SET `parent_list` = replace(`parent_list`,'$str','$new_str') where `parent_list` like '".$str.',%'."' || `parent_list` = $str";
        $connection=\Yii::$app->db;
        $command=$connection->createCommand($sql);
        $rowCount=$command->execute();
        $transaction->commit();
        return $this->respondJson(0, '审核成功');
    }
    // 节点转让审核不通过
    public function actionExamineOff()
    {
        $nodeId = $this->pInt('id');
        if (empty($nodeId)) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $remark = $this->pString('remark');
        if (empty($remark)) {
            return $this->respondJson(1, '原因不能为空');
        }
        $data = BNodeTransfer::find()->where(['id' => $nodeId])->one();
        if (empty($data)) {
            return $this->respondJson(1, '不存在的申请');
        }

        $data->status = BNodeTransfer::STATUS_FAIL;
        $data->status_remark = $remark;
        $data->examine_time = time();
        $data->examine_user =  $this->user_id;
        if (!$data->save()) {
            return $this->respondJson(1, '审核失败', $data->getFirstErrorText());
        }
        return $this->respondJson(0, '审核成功');
    }
}
