<?php

namespace app\controllers;

use yii\helpers\ArrayHelper;
use common\components\FuncHelper;
use common\models\business\BVote;
use common\services\SettingService;

class VoteController extends BaseController
{
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();
        
        $behaviors = [];
        // 需登录访问
        $authActions = [
            'index'
        ];

        if (isset($parentBehaviors['authenticator']['isThrowException'])) {
            if (!in_array(\Yii::$app->controller->action->id, $authActions)) {
                $parentBehaviors['authenticator']['isThrowException'] = true;
            }
        }

        return ArrayHelper::merge($parentBehaviors, $behaviors);
    }

    /**
     * 贡献榜 无需登录可查看
     *
     * @return void
     */
    public function actionIndex()
    {
        $page = $this->pInt('page', 1);
        $pageSize = $this->pInt('page_size', 15);
        $voteShowType = $this->pString('type', 'all');
        // 容器
        $data = [];
        
        // 刷新时间获取，更新时间
        $endUpdate = SettingService::get('vote', 'end_update_time');
        if (!is_object($endUpdate) && empty($endUpdate->value)) {
            return $this->respondJson(1, "投票更新时间未设定");
        }
        $updateTime = (int) $endUpdate->value;
        $data['countTime'] = FuncHelper::formateDate($updateTime);
        $voteModel = BVote::find()
        ->select(['user_id', 'node_id', 'create_time', 'type', 'status'])
        ->where(['<=', 'create_time', $updateTime])
        ->with('user')
        ->active();
        if ($voteShowType === 'pay') {
            $voteModel->where(['type' => BVote::TYPE_PAY]);
        }
        $voteModel->addSelect(['SUM(vote_number) as vote_number']);
        $voteModel->groupBy('user_id');
        $voteModel->orderBy(['vote_number' => SORT_ASC]);
        $data['count'] = $voteModel->count();
        $voteModel->page($page, $pageSize);
        $voteDataModel = $voteModel->all();
        
        if (!is_object(reset($voteDataModel))) {
            return $this->respondJson(0, '记录为空');
        }
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
     * 我的投票劵获取和使用列表
     *
     * @return void
     */
    public function actionVoucher()
    {
        // 返回容器
        $data = [];
        $type = $this->pInt('type', 1);
        $page = $this->pInt('page', 1);
        $pageSize = $this->pInt('page_size', 15);
        $userModel = $this->user;
        // $voucherModel->sum('voucher_num - use_voucher');
        if ((bool) $type) {
            $voucherModel = $userModel->getVouchers();
            $voucherModel->alias('vh')
            ->select(['vh.voucher_num', 'u.mobile', 'vh.node_id', 'vh.create_time'])
            ->joinWith(['node n' => function ($query) {
                $query->joinWith(['user u']);
            }]);
            $data['count'] = $voucherModel->count();
            $data['list'] = $voucherModel
            ->page($page, $pageSize)
            ->asArray()->all();
            foreach ($data['list'] as &$voucher) {
                $voucher['mobile'] = substr_replace($voucher['mobile'], '****', 3, 4);
                $voucher['create_time'] = FuncHelper::formateDate($voucher['create_time']);
                unset($voucher['node']);
                unset($voucher['node_id']);
            }
        } else {
            $voucherDetailModel = $userModel->getVoucherDetails()
            ->alias('vd')
            ->joinWith(['node n'])
            ->select(['n.name', 'vd.amount', 'vd.create_time', 'vd.node_id']);
            
            $data['count'] = $voucherDetailModel->count();
            $data['list'] = $voucherDetailModel
            ->page($page, $pageSize)
            ->asArray()->all();
            foreach ($data['list'] as &$voucherDetail) {
                $voucherDetail['create_time'] = FuncHelper::formateDate($voucherDetail['create_time']);
                unset($voucherDetail['node']);
                unset($voucherDetail['node_id']);
            }
        }
        return $this->respondJson(0, '获取成功', $data);
    }

    /**
     * 我的投票劵信息
     *
     * @return void
     */
    public function actionVoucherInfo()
    {
        $userModel = $this->user;
        $voucher = $userModel->getVouchers()
        ->select(['SUM(vh.voucher_num) voucher_num', 'SUM(vd.amount) use_amount', 'vh.user_id'])
        ->alias('vh')
        ->joinWith(['user u' => function($query) {
            $query->joinWith(['voucherDetails vd']);
        }])
        ->one();
        $data['count'] = (int) $voucher->voucher_num - $voucher->use_amount;
        return $this->respondJson(0, '获取成功', $data);
        exit;
    }
}
