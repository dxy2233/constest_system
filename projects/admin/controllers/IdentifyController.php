<?php
namespace admin\controllers;

use common\services\AclService;
use common\services\TicketService;
use yii\helpers\ArrayHelper;
use common\models\business\BUser;
use common\models\business\BNode;
use common\models\business\BVote;
use common\models\business\BNotice;
use common\models\business\BUserIdentify;
use common\models\business\BUserWallet;
use common\models\business\BUserVoucher;
use common\models\business\BUserCurrency;
use common\models\business\BVoucher;
use common\models\business\BUserCurrencyDetail;
use common\models\business\BVoucherDetail;
use common\models\business\BUserCurrencyFrozen;
use common\models\business\BUserRecommend;
use common\components\FuncHelper;

/**
 * Site controller
 */
class IdentifyController extends BaseController
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
        $find = BUser::find()
        ->from(BUser::tableName()." A")
        ->join('left join', BUserIdentify::tableName().' B', 'B.user_id = A.id');
        $page = $this->pInt('page', 0);
        $status = $this->pInt('status', 0);
        $find->andWhere(['B.status' => $status]);
        $find->select(['A.mobile','B.realname','B.number','B.status','B.create_time','A.id', 'B.examine_time']);
        $searchName = $this->pString('searchName');
        
        if ($searchName != '') {
            $find->andWhere(['or',['like','A.username',$searchName],['like', 'A.mobile', $searchName],['like', 'B.number', $searchName]]);
        }
        $count = $find->count();
        $find->orderBy('B.create_time DESC');
        if ($page != 0) {
            $find->page($page);
        }
        $list = $find->asArray()->all();
        foreach ($list as &$v) {
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $v['examine_time'] =  $v['examine_time'] == 0 ? '-' :date('Y-m-d H:i:s', $v['examine_time']);
            $v['status'] = BUserIdentify::getStatus($v['status']);
        }
        $return = ['count' => $count, 'list' => $list];
        return $this->respondJson(0, '获取成功', $return);
    }

    public function actionDetail()
    {
        $user_id = $this->pInt('user_id');
        if (empty($user_id)) {
            return $this->respondJson(1, '用户ID不能为空');
        }
        $data = BUserIdentify::find()->where(['user_id' => $user_id])->asArray()->one();
        if (empty($data)) {
            return $this->respondJson(1, '此用户没有实名信息');
        }
        return $this->respondJson(0, '获取成功', $data);
    }
    // 审核不通过
    public function actionExamineOff()
    {
        $user_id = $this->pInt('user_id');
        if (empty($user_id)) {
            return $this->respondJson(1, '用户ID不能为空');
        }
        $remark = $this->pString('remark');
        if (empty($remark)) {
            return $this->respondJson(1, '原因不能为空');
        }
        $data = BUserIdentify::find()->where(['user_id' => $user_id])->one();
        if (empty($data)) {
            return $this->respondJson(1, '不存在的节点');
        }
        $data->status = BUserIdentify::STATUS_FAIL;
        $data->status_remark = $remark;
        if (!$data->save()) {
            return $this->respondJson(1, '审核失败', $data->getFirstErrorText());
        }
        return $this->respondJson(0, '审核成功');
    }
    // 审核通过
    public function actionExamineOn()
    {
        $user_id = $this->pInt('user_id');
        if (empty($user_id)) {
            return $this->respondJson(1, '用户ID不能为空');
        }

        $data = BUserIdentify::find()->where(['user_id' => $user_id])->one();
        if (empty($data)) {
            return $this->respondJson(1, '不存在的节点');
        }
        $data->status = BUserIdentify::STATUS_ACTIVE;
        $data->status_remark = '已通过';
        if (!$data->save()) {
            return $this->respondJson(1, '审核失败', $data->getFirstErrorText());
        }
        $user = BUser::find()->where(['id' => $user_id])->one();
        $user->is_identified = BNotice::STATUS_ACTIVE;
        if (!$user->save()) {
            return $this->respondJson(1, '审核失败', $user->getFirstErrorText());
        }
        return $this->respondJson(0, '审核成功');
    }
}
