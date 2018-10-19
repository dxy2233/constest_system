<?php

namespace app\controllers;

use yii\helpers\ArrayHelper;
use common\services\VoteService;
use common\components\FuncHelper;
use common\models\business\BVote;
use common\services\SettingService;
use common\models\business\BHistory;
use common\models\business\BCurrency;

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

    /**
     * 我的投票记录
     *
     * @return void
     */
    public function actionLogs()
    {
        // 返回容器
        $data = [];
        // 查询类型
        $type = $this->pInt('type', 1);
        $page = $this->pInt('page', 1);
        $pageSize = $this->pInt('page_size', 15);
        $userModel = $this->user;
        $voteModel = $userModel->getVotes()
        ->select(['v.*', 'n.name', 'nt.name as type_name'])
        ->alias('v')
        ->joinWith(['node n' => function($query) {
            $query->joinWith(['nodeType nt']);
        }]);
        if ($type) {
            // 默认为投出的
           $voteModel->active(BVote::STATUS_ACTIVE, 'v.');
        } else {
            // 赎回中以及赎回
            $voteModel->where(['v.status' => [BVote::STATUS_INACTIVE, BVote::STATUS_INACTIVE_ING]]);
        }
        $data['count'] = $voteModel->count();
        $data['list'] = $voteModel->page($page, $pageSize)
        ->asArray()
        ->all();
        foreach ($data['list'] as &$vote) {
            $vote['undo_time'] = FuncHelper::formateDate($vote['undo_time']);
            $vote['create_time'] = FuncHelper::formateDate($vote['create_time']);
            $vote['status_str'] = BVote::getStatus($vote['status']);
            $vote['type_str'] = BVote::getType($vote['type']);
            $vote['is_revoke'] = in_array($vote['status'], [BVote::STATUS_INACTIVE, BVote::STATUS_INACTIVE_ING]) ? false : in_array($vote['type'], BVote::IS_REVOKE);
            unset($vote['node'], $vote['user_id'], $vote['node_id'], $vote['consume'], $vote['type'], $vote['status']);
        }
        // var_dump($voteList);exit;
        return $this->respondJson(0, '获取成功', $data);

    }

    /**
     * 判断投票是否能赎回
     *
     * @return void
     */
    public function actionHasRevoke()
    {
        $voteId = $this->pInt('id', 1);
        $userModel = $this->user;

        $voteHas = VoteService::hasRevoke($userModel, $voteId);
        if ($voteHas->code) {
            return $this->respondJson($voteHas->code, $voteHas->msg, $voteHas->content);
        }
        if ($voteHas->content) {
            return $this->respondJson(0, '获取结果', true);
        } else {
            return $this->respondJson(0, '获取结果', false);
        }
    }


    /**
     * 我的投票赎回操作
     *
     * @return void
     */
    public function actionRevokeVote()
    {
        $voteId = $this->pInt('id', 1);
        $hasRevoke = $this->actionHasRevoke();
        if (!$this->respondData['content']) {
            return $this->respondJson(1, '暂时不能赎回');
        }

        $voteModel = BVote::findOne($voteId);
        if (is_null($voteModel) || in_array($voteModel->status, [BVote::STATUS_INACTIVE, BVote::STATUS_INACTIVE_ING])) {
            return $this->respondJson(1, '该投票状态不能更改');
        }
        // 赎回中状态
        $voteModel->status = BVote::STATUS_INACTIVE_ING;
        if (!$voteModel->save()) {
            return $this->respondJson(1, '赎回失败', $voteModel->getFirstErrors());
        }

        return $this->respondJson(0, '赎回成功');

    }

    /**
     * 投票方式显示
     *
     * @return void
     */
    public function actionTypes()
    {
        $userModel = $this->user;
        $ordinaryPrice = SettingService::get('vote', 'ordinary_price');
        $paymentPrice = SettingService::get('vote', 'payment_price');
        $this->actionVoucherInfo();
        $voucherNumber = $this->respondData['content']['count'];
        // 返回容器
        $data = [
            [
                'id' => BVote::TYPE_ORDINARY,
                'name' => BVote::getType(BVote::TYPE_ORDINARY),
                'scaling' => $ordinaryPrice->value . 'GRT=1票 可赎回',
            ],
            [
                'id' => BVote::TYPE_PAY,
                'name' => BVote::getType(BVote::TYPE_PAY),
                'scaling' => '消耗' . $paymentPrice->value . 'GRT=10票',
            ],
            [
                'id' => BVote::TYPE_VOUCHER,
                'name' => BVote::getType(BVote::TYPE_VOUCHER),
                'scaling' => '拥有数量 ' . $voucherNumber. '票',
            ],
        ];
        

        return $this->respondJson(0, '获取成功', $data);
    }

    public function actionTypeInfo()
    {
        $data = [];
        $type = $this->pInt('type', 1);
        $userModel = $this->user;
        $paymentPrice = SettingService::get('vote', 'payment_price');
        $voteCurrencyCode = SettingService::get('vote', 'vote_currency')->value ?? 'grt';
        // 返回容器
        $data['amount'] = 0;
        $data['number'] = 0;
        switch($type) {
            case BVote::TYPE_ORDINARY:
                $ordinaryPrice = 1 / (float) SettingService::get('vote', 'ordinary_price')->value;
                $currencyId = BCurrency::find()->select(['id'])->where(['code' => $voteCurrencyCode])->scalar();
                $userCurrencyModel = $userModel->getUserCurrency()
                ->where(['currency_id' => $currencyId]);
                $userCurrencyInfo = $userCurrencyModel->one();
                if (!is_null($userCurrencyInfo)) {
                    $data['amount'] = $userCurrencyInfo->use_amount;
                    $data['number'] = $userCurrencyInfo->use_amount;
                } 
            break;
            case BVote::TYPE_PAY:
            break;
            case BVote::TYPE_VOUCHER:
            break;
        }
        return $this->respondJson(0, '获取成功', $data);
    }
}
