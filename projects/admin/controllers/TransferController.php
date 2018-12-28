
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
use common\models\business\BNodeTransfer;
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
        ->select(['A.user_id as from_id', 'B.name', 'C.realname', 'C.number'])
        ->join('left join', BNodeType::tableName().' B', 'A.type_id = B.id')
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
        $transfer = new BNodeTransfer();
        $transfer->from_user_id = $from_id;
        $transfer->to_user_id = $to_id;
        $transfer->node_id = $node->id;
        $transfer->images = explode(',', $images);
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
        ->select(['A.id', 'C.name as type_name', 'D.mobile as from_user_mobile', 'E.realname as from_user_name', 'F.mobile as to_user_mobile', 'G.realname as to_user_name', 'A.status', 'A.create_time', 'A.examine_time'])
        ->join('left join', BNode::tableName().' B', 'A.node_id = B.id')
        ->join('left join', BNodeType::tableName().' C', 'B.type_id = C.id')
        ->join('left join', BUser::tableName().' D', 'A.from_user_id = D.id')
        ->join('left join', BUserIdentify::tableName().' E', 'A.from_user_id = E.user_id && E.status = '.BUserIdentify::STATUS_ACTIVE)
        ->join('left join', BUser::tableName().' F', 'A.to_user_id = F.id')
        ->join('left join', BUserIdentify::tableName().' G', 'A.to_user_id = G.user_id && G.status = '.BUserIdentify::STATUS_ACTIVE);
        if ($searchName != '') {
            $find->andWhere(['or', ['like', 'B.name', $searchName], ['like', 'C.mobile', $searchName]]);
        }
        $page = $this->pInt('page', 1);
        $count = $find->count();
        $order = $this->gString('order');
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
    }
}
