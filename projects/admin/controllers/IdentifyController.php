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
use common\models\business\BNodeRecommend;
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
            'download'
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

        $page = $this->pInt('page', 1);
        $status = $this->pInt('status', 0);
        $find->andWhere(['B.status' => $status]);
        $find->select(['A.mobile','B.realname','B.number','B.status','B.create_time','B.id', 'B.examine_time']);
        $searchName = $this->pString('searchName');
        
        if ($searchName != '') {
            $find->andWhere(['or',['like','B.realname',$searchName],['like', 'A.mobile', $searchName],['like', 'B.number', $searchName]]);
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

    // 实名认证导出
    public function actionDownload()
    {
        $down = $this->checkDownloadCode();
        if (!$down) {
            exit('验证失败');
        }
        $find = BUser::find()
        ->from(BUser::tableName()." A")
        ->join('left join', BUserIdentify::tableName().' B', 'B.user_id = A.id');
        $id = $this->gString('id');
        if ($id != '') {
            $find->andWhere(['A.id' => explode(',', $id)]);
        }
        $status = $this->gInt('status', 0);
        $find->andWhere(['B.status' => $status]);
        $find->select(['A.mobile','B.realname','B.number','B.status','B.create_time','B.id', 'B.examine_time']);
        $searchName = $this->gString('searchName');
        
        if ($searchName != '') {
            $find->andWhere(['or',['like','B.realname',$searchName],['like', 'A.mobile', $searchName],['like', 'B.number', $searchName]]);
        }
        $find->orderBy('B.create_time DESC');
        $list = $find->asArray()->all();
        foreach ($list as &$v) {
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
            $v['examine_time'] =  $v['examine_time'] == 0 ? '-' :date('Y-m-d H:i:s', $v['examine_time']);
            $v['status'] = BUserIdentify::getStatus($v['status']);
        }

//        return $this->respondJson(0, '获取成功', $list);
        
        $headers = ['mobile'=> '手机号','realname' => '姓名', 'number' => '身份证号', 'status' => '状态', 'create_time' => '提交时间', 'examine_time' => '审核时间'];

        $this->download($list, $headers, '实名认证列表'.date('YmdHis'));

        return;
    }


    // 获取用户实名认证信息，包含未通过及未审核
    public function actionDetail()
    {
        $user_id = $this->pInt('user_id');
        if (empty($user_id)) {
            return $this->respondJson(1, 'ID不能为空');
        }
        $data = BUserIdentify::find()->where(['id' => $user_id])->orderBy('id desc')->asArray()->one();
        if (empty($data)) {
            return $this->respondJson(1, '没有实名信息');
        }
        $data['pic_back'] = FuncHelper::getImageUrl($data['pic_back'], 640, 640);
        $data['pic_front'] = FuncHelper::getImageUrl($data['pic_front'], 640, 640);
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
        $data = BUserIdentify::find()->where(['id' => $user_id])->orderBy('id DESC')->one();
        if (empty($data)) {
            return $this->respondJson(1, '不存在的内容');
        }
        $data->status = BUserIdentify::STATUS_FAIL;
        $data->status_remark = BUserIdentify::getStatus(BUserIdentify::STATUS_FAIL);
        $data->remark = $remark;
        $data->examine_time = time();
        $data->audit_admin_id = $this->user_id;
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

        $data = BUserIdentify::find()->where(['id' => $user_id])->orderBy('id DESC')->one();
        if (empty($data)) {
            return $this->respondJson(1, '不存在的节点');
        }
        $data->status = BUserIdentify::STATUS_ACTIVE;
        $data->status_remark = '已通过';
        $data->examine_time = time();
        $data->audit_admin_id = $this->user_id;
        if (!$data->save()) {
            return $this->respondJson(1, '审核失败', $data->getFirstErrorText());
        }
        $user = BUser::find()->where(['id' => $data->user_id])->one();
        $user->is_identified = BNotice::STATUS_ACTIVE;
        if (!$user->save()) {
            return $this->respondJson(1, '审核失败', $user->getFirstErrorText());
        }
        return $this->respondJson(0, '审核成功');
    }
}
