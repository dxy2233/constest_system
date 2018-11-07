<?php

namespace app\controllers;

use yii\helpers\ArrayHelper;
use common\services\NodeService;
use common\services\UserService;
use common\services\VoteService;
use common\components\FuncHelper;
use common\models\business\BNode;
use common\models\business\BCycle;
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
            'index',
            'count-time',
        ];

        if (isset($parentBehaviors['authenticator']['isThrowException'])) {
            if (!in_array(\Yii::$app->controller->action->id, $authActions)) {
                $parentBehaviors['authenticator']['isThrowException'] = true;
            }
        }

        return ArrayHelper::merge($parentBehaviors, $behaviors);
    }

    /**
     * 投票统计剩余时间或是否开启
     *
     * @return void
     */
    public function actionCountTime()
    {
        //$countTime = (int) SettingService::get('vote', 'count_time')->value;
        $cycle = BCycle::find()->all();
        $countTime = NOW_TIME;
        foreach ($cycle as $v) {
            if ($v->cycle_start_time <= time() && $v->cycle_end_time>=time()) {
                $countTime = $v->cycle_end_time;
            }
        }
        $downTime = $countTime - NOW_TIME;
        $data['count_down_is_open'] = (bool) SettingService::get('vote', 'count_down_is_open')->value;
        $data['down_time'] = $downTime > 0 ? $downTime : 0;
        return $this->respondJson(0, '获取成功', $data);
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
        
        $voteModel = BVote::find()
        ->select(['user_id', 'node_id', 'create_time', 'type', 'status'])
        ->with('user')
        ->active();
        if ($voteShowType === 'pay') {
            $voteModel->where(['type' => BVote::TYPE_PAY]);
        }
        $voteModel->addSelect(['SUM(vote_number) as vote_number']);
        $voteModel->groupBy('user_id');
        $voteModel->orderBy(['vote_number' => SORT_DESC]);
        $data['count'] = $voteModel->count();
        $voteModel->page($page, $pageSize);
        $voteDataModel = $voteModel->all();
        
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
        $type = $this->pInt('type', 0);
        $page = $this->pInt('page', 1);
        $pageSize = $this->pInt('page_size', 15);
        $userModel = $this->user;
        // var_dump($type, (bool) $type);exit;
        if ((bool) $type) {
            $voucherModel = $userModel->getVouchers();
            $voucherModel->alias('vh')
            ->select(['vh.voucher_num', 'u.mobile', 'vh.node_id', 'vh.create_time'])
            ->joinWith(['node n' => function ($query) {
                $query->joinWith(['user u'], false);
            }], false);
            $voucherModel->orderBy(['create_time' => SORT_DESC]);
            $data['count'] = $voucherModel->count();
            $data['list'] = $voucherModel
            ->page($page, $pageSize)
            ->asArray()->all();
            foreach ($data['list'] as &$voucher) {
                $voucher['mobile'] = substr_replace($voucher['mobile'], '****', 3, 4);
                $voucher['create_time'] = FuncHelper::formateDate($voucher['create_time']);
                $voucher['voucher_num'] = '+' . $voucher['voucher_num'];
                unset($voucher['node']);
                unset($voucher['node_id']);
            }
        } else {
            $voucherDetailModel = $userModel->getVoucherDetails()
            ->alias('vd')
            ->joinWith(['node n'], false)
            ->select(['n.name', 'vd.amount', 'vd.create_time', 'vd.node_id']);
            $voucherDetailModel->orderBy(['create_time' => SORT_DESC]);
            $data['count'] = $voucherDetailModel->count();
            $data['list'] = $voucherDetailModel
            ->page($page, $pageSize)
            ->asArray()->all();
            foreach ($data['list'] as &$voucherDetail) {
                $voucherDetail['create_time'] = FuncHelper::formateDate($voucherDetail['create_time']);
                $voucherDetail['amount'] = '-' . $voucherDetail['amount'];
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
        UserService::resetVoucher($userModel->id);
        $userVoucher = $userModel->userVoucher;
        $data['count'] = (int) $userVoucher->surplus_amount;
        return $this->respondJson(0, '获取成功', $data);
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
        $type = $this->pInt('type', 0);
        $page = $this->pInt('page', 1);
        $pageSize = $this->pInt('page_size', 15);
        $userModel = $this->user;
        $voteModel = $userModel->getVotes()
        ->select(['v.*', 'n.name', 'nt.name as type_name'])
        ->alias('v')
        ->joinWith(['node n' => function ($query) {
            $query->joinWith(['nodeType nt'], false);
        }], false);
        if ($type) {
            // 默认为投出的
            $voteModel->active(BVote::STATUS_ACTIVE, 'v.');
        } else {
            // 赎回中以及赎回
            $voteModel->where(['v.status' => [BVote::STATUS_INACTIVE, BVote::STATUS_INACTIVE_ING]]);
        }
        $voteModel->orderBy(['create_time' => SORT_DESC]);
        $data['count'] = $voteModel->count();
        $data['list'] = $voteModel->page($page, $pageSize)
        ->asArray()
        ->all();
        // 创建一个闭包函数
        $history = function (int $voteId) {
            $historyModel = BHistory::find()->select('id')
            ->where(['node_id' => $nodeId])
            ->andWhere(['>', 'create_time', $voteTime])
            ->orderBy(['update_number' => SORT_DESC]);
            return $historyModel->exists();
        };
        foreach ($data['list'] as &$vote) {
            if (in_array($vote['status'], [BVote::STATUS_INACTIVE, BVote::STATUS_INACTIVE_ING])) {
                $vote['is_revoke'] = false;
            } else {
                $vote['is_revoke'] = in_array($vote['type'], BVote::IS_REVOKE) ? VoteService::hasRevoke($this->user, $vote['id']) : false;
            }
            $vote['undo_time'] = FuncHelper::formateDate($vote['undo_time']);
            $vote['create_time'] = FuncHelper::formateDate($vote['create_time']);
            $vote['status_str'] = BVote::getStatus($vote['status']);
            $vote['type_str'] = BVote::getType($vote['type']);
            // $vote['is_revoke'] = in_array($vote['status'], [BVote::STATUS_INACTIVE, BVote::STATUS_INACTIVE_ING]) ? false : in_array($vote['type'], BVote::IS_REVOKE);
            unset($vote['user_id'], $vote['node_id'], $vote['consume'], $vote['status'], $vote['unit_code']);
        }
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

        // 赎回时间设定
        $remokeDay = (int) SettingService::get('vote', 'remoke_day')->value;
        $voteModel->undo_time = NOW_TIME + $remokeDay * 86400;
        // 赎回中状态
        $voteModel->status = BVote::STATUS_INACTIVE_ING;
        if (!$voteModel->save()) {
            return $this->respondJson(1, '赎回失败', $voteModel->getFirstErrors());
        }
        // 刷新节点投票排行
        NodeService::RefreshPushRanking($voteModel->node_id);
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
                'scaling' => '消耗' . $paymentPrice->value . 'GRT=1票',
            ],
            [
                'id' => BVote::TYPE_VOUCHER,
                'name' => BVote::getType(BVote::TYPE_VOUCHER),
                'scaling' => '拥有数量 ' . $voucherNumber. '票',
            ],
        ];
        

        return $this->respondJson(0, '获取成功', $data);
    }

    /**
     * 投票类型具体参数
     *
     * @return void
     */
    public function actionTypeInfo()
    {
        $data = [];
        $type = $this->pInt('type', 1);
        $userModel = $this->user;
        $voteCurrencyCode = SettingService::get('vote', 'vote_currency')->value ?? 'grt';
        // 返回容器
        $data['amount'] = 0;
        $data['number'] = 0;
        $data['unit_code'] = '票';
        $data['show_currency'] = true;
        $scaling = 1;
        // 当前节点最后生成快照的时间
        //$historyLastTime = BHistory::find()->max('create_time');
        if ($type === BVote::TYPE_ORDINARY) {
            // 持有投票
            $scaling = (float) SettingService::get('vote', 'ordinary_price')->value;
            $singleMax = (float) SettingService::get('vote', 'single_total')->value;
        } elseif ($type === BVote::TYPE_PAY) {
            // 支付投票
            $scaling = (float) SettingService::get('vote', 'payment_price')->value;
            $singleMax = (float) SettingService::get('vote', 'single_pay_total')->value;
            // 货币单位
            $data['unit_code'] = strtoupper($voteCurrencyCode);
        } else {
            $this->actionVoucherInfo();
            $voucherNumber = $this->respondData['content']['count'];
            $data['show_currency'] = false;
            $data['amount'] = $voucherNumber;
            $data['number'] = $voucherNumber;
            $data['surplus_number'] = $voucherNumber;
            return $this->respondJson(0, '获取成功', $data);
        }
        
        $data_2 = BCycle::find()->where(['>', 'tenure_end_time', time()])->orderBy('id asc')->all();
        $bool = false;
        $historyLastTime = time();
        foreach ($data_2 as $v) {
            if ($v->cycle_start_time <= time() && $v->cycle_end_time >= time()) {
                $bool = true;
                $historyLastTime = $v->cycle_start_time;
            }
        }
        $currencyId = BCurrency::find()->select(['id'])->where(['code' => $voteCurrencyCode])->scalar();
        $userCurrencyModel = $userModel->getUserCurrency()
        ->where(['currency_id' => $currencyId]);
        $userCurrencyInfo = $userCurrencyModel->one();
        if (!is_null($userCurrencyInfo)) {
            if ($bool) {
                // 计算指定时间投票用户投票总和
                $countNumber = BVote::find()
                ->active()
                ->where(['type' => $type, 'user_id' => $userModel->id])
                ->andWhere(['>=', 'create_time', $historyLastTime])
                ->sum('vote_number') ?? 0;
                $useAmount = round($userCurrencyInfo->use_amount, 8);
                $numberAll = $useAmount / $scaling;
                $surplusNumber = $singleMax / $scaling - $countNumber;
                if ($surplusNumber <= 0) {
                    $surplusNumber = 0;
                } elseif ($surplusNumber > $numberAll) {
                    $surplusNumber = $numberAll;
                }
                $data['number'] = $surplusNumber;
                $data['amount'] = $surplusNumber * $scaling;
            } else {
                $useAmount = round($userCurrencyInfo->use_amount, 8);
                $numberAll = $useAmount / $scaling;
                $data['number'] = $numberAll;
                $data['amount'] = $userCurrencyInfo->use_amount;
            }
        }
        return $this->respondJson(0, '获取成功', $data);
    }

    /**
     * 投票提交
     *
     * @return void
     */
    public function actionSubmit()
    {
        $userModel = $this->user;
        $nodeId = $this->pInt('node_id', false);
        if (!$nodeId) {
            return $this->respondJson(1, '节点不能为空');
        }
        // 当前节点模型
        $nodeModel = BNode::findOne($nodeId);
        if (is_null($nodeModel)) {
            return $this->respondJson(1, '节点不能为空');
        }
        // 当前节点类型是否开启投票功能
        $nodeTypeModel = $nodeModel->nodeType;
        if (is_null($nodeTypeModel) && !$nodeTypeModel->is_vote) {
            return $this->respondJson(1, '该节点不能投票');
        }
        // 自己不能给自己投票
        // if ($nodeModel->user_id == $userModel->id) {
        //     return $this->respondJson(1, '不能投票给自己');
        // }
        $type = $this->pInt('type', false);
        if (!$type) {
            return $this->respondJson(1, '投票方式不能为空');
        }
        $number = $this->pInt('number', 0);
        if ($number <= 0) {
            return $this->respondJson(1, '投票数量不能为小于等于0');
        }
        $payPass = $this->pInt('pass', false);
        if (!$payPass) {
            return $this->respondJson(1, '支付密码不能为空');
        }
        // 验证用户交易密码是否一致
        $transPwdEncryption = UserService::generateTransPwdHash($userModel->pwd_salt, $payPass);
        if ($userModel->trans_password !== $transPwdEncryption) {
            return $this->respondJson(1, '支付密码错误');
        }

        if (in_array($type, [BVote::TYPE_ORDINARY, BVote::TYPE_PAY])) {
            // 当前节点最后生成快照的时间
            //$historyLastTime = BHistory::find()->where(['node_id' => $nodeId])->max('create_time');
            // 参与投票的货币
            $voteCurrencyCode = SettingService::get('vote', 'vote_currency')->value ?? 'grt';
            $currencyId = (int) BCurrency::find()->select(['id'])->where(['code' => $voteCurrencyCode])->scalar();
            // 消费货币是先进行资金重算
            UserService::resetCurrency($userModel->id, $currencyId);
            $userCurrencyModel = $userModel->getUserCurrency()
            ->where(['currency_id' => $currencyId]);
            $userCurrencyInfo = $userCurrencyModel->one();
            if (is_null($userCurrencyInfo)) {
                return $this->respondJson(1, '没有可用的货币');
            }
            if ($type === BVote::TYPE_ORDINARY) {
                // 持有投票
                $scaling = (float) SettingService::get('vote', 'ordinary_price')->value;
                $singleMax = (float) SettingService::get('vote', 'single_total')->value;
            } elseif ($type === BVote::TYPE_PAY) {
                // 支付投票
                $scaling = (float) SettingService::get('vote', 'payment_price')->value;
                $singleMax = (float) SettingService::get('vote', 'single_pay_total')->value;
            }

            // 票数转换成 货币数量
            $currencyAmount = round($number * $scaling, 8);
            // 本次竞选剩余可支付货币数量
            
            $data = BCycle::find()->where(['>', 'tenure_end_time', time()])->orderBy('id asc')->all();
            $bool = false;
            $historyLastTime = time();
            foreach ($data as $v) {
                if ($v->cycle_start_time <= time() && $v->cycle_end_time >= time()) {
                    $bool = true;
                    $historyLastTime = $v->cycle_start_time;
                }
            }
            // 计算指定时间投票用户投票总和
            $countConsume = BVote::find()
                        ->active()
                        ->where(['type' => $type, 'user_id' => $userModel->id])
                        ->andWhere(['>=', 'create_time', $historyLastTime])
                        ->sum('consume') ?? 0;
            $surplusMax = round($singleMax - $countConsume, 8);
            if ($bool && $currencyAmount > $surplusMax) {
                return $this->respondJson(1, "已达本次投票竞选上限");
            }
            // 获取总票数
            $useAmount = round($userCurrencyInfo->use_amount / $scaling, 8);
            if ($useAmount < $number) {
                return $this->respondJson(1, '货币量不足');
            }
            $voteRes = [
                'vote_number' => $number,
                'amount' => $currencyAmount,
                'currency_id' => $currencyId,
                'node_id' => $nodeId,
                'unit_code' => $voteCurrencyCode,
            ];
            $voteAction = VoteService::currencyVote($userModel, $voteRes, $type);
            if ($voteAction->code) {
                return $this->respondJson(1, '投票失败');
            }
        } else {
            $this->actionVoucherInfo();
            $voucherNumber = $this->respondData['content']['count'];
            if ($voucherNumber < $number) {
                return $this->respondJson(1, '投票劵不足');
            }
            $voteRes = [
                'vote_number' => $number,
                'node_id' => $nodeId,
                'unit_code' => 'voucher',
            ];
            $voteAction = VoteService::voucherVote($userModel, $voteRes);
            if ($voteAction->code) {
                return $this->respondJson(1, '投票失败');
            }
        }
        // 刷新节点投票排行
        NodeService::RefreshPushRanking($nodeId);
        return $this->respondJson(0, '投票成功');
    }
}
