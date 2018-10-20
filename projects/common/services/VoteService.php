<?php

namespace common\services;

use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use common\components\FuncResult;
use common\models\business\BNode;
use common\models\business\BUser;
use common\models\business\BVote;
use common\models\business\BVoucherDetail;
use common\models\business\BWalletJingtum;
use common\models\business\BUserCurrencyDetail;
use common\models\business\BUserCurrencyFrozen;

class VoteService extends ServiceBase
{
    public static function getInstance(): VoteService
    {
        self::$instance = new self();
        self::$instance->init();

        return self::$instance;
    }

    /**
     * 校验投票是否能被赎回
     *
     * @param BUser $userModel
     * @param integer $id
     * @return boolean
     */
    public static function hasRevoke(BUser $userModel, int $voteId)
    {
        $voteModel = $userModel->getVotes()
        ->active()
        ->where(['type' => BVote::TYPE_ORDINARY, 'id' => $voteId]);
        $vote = $voteModel->one();
        if (is_null($vote)) {
            return new FuncResult(0, '该投票不存在或不能赎回', false);
        }
        $historyModelExists = $vote->getHistorys()
        ->select('id')
        ->andWhere(['>', 'create_time', $vote->create_time])
        ->orderBy(['update_number' => SORT_DESC])
        ->exists();
        return new FuncResult(0, '校验结果', $historyModelExists);
    }
    /**
     * 货币投票
     *
     * @param BUser $userModel
     * @param array $data
     * @param integer $type
     * @return void
     */
    public static function currencyVote(BUser $userModel, array $data = [], int $type = 1)
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $isFrozen = true;
            $voteModel = new BVote();
            $voteModel->node_id = $data['node_id'];
            $voteModel->type = $type;
            $voteModel->status = BVote::STATUS_ACTIVE;
            $voteModel->vote_number = $data['vote_number'];
            if ($type == BVote::TYPE_PAY) {
                $isFrozen = false;
                $voteModel->consume = $data['amount'];
            }
            $voteModel->create_time = NOW_TIME;
            $voteModel->user_id = $userModel->id;
            if (!$voteModel->save()) {
                throw new ErrorException('投票失败', $voteModel->getFirstError());
            }
            $poundage = (int) SettingService::get('vote', 'vote_poundage')->value;
            // 发送方账户公钥
            $addressModel = $userModel->userRechargeAddress;
            if (is_null($addressModel)) {
                throw new ErrorException('wallet address no found');
            }
            $privateKey = BWalletJingtum::find()->select(['secret'])->where(['address' => $addressModel->address])->scalar();
            if (is_null($privateKey)) {
                throw new ErrorException('wallet private no found');
            }
            $poundage = 0; // 手续费
            $res = [
                'id' => $voteModel->id,
                'currency_id' => $data['currency_id'],
                'user_id' => $userModel->id,
                'type' => BUserCurrencyDetail::$TYPE_VOTE,
                'amount' => $data['amount'], // 总数量
                'poundage' => $poundage, // 手续费
                'privateKey' => $privateKey, // 发送方私钥
                'source_address' => $addressModel->address, // 发送方地址
                'remark' => BUserCurrencyDetail::getType(BUserCurrencyDetail::$TYPE_VOTE),
                'status' => BUserCurrencyDetail::$STATUS_EFFECT_SUCCESS,
                'status_remark' => '确认',
                'create_time' => NOW_TIME,
                'update_time' => NOW_TIME,
                'user_recharge' => $isFrozen ? 'voto_frozen' : 'voto_trans',
                'poundage' => $poundage, // 手续费
            ];
            $currencyTrans = WithdrawService::withdrawCurrencyVote($res, $isFrozen);
            if ($currencyTrans->code) {
                throw new ErrorException('划账失败 '.$currencyTrans->msg);
            }
            $transaction->commit();
            return new FuncResult(0, '投票成功');
        } catch (\Exception $e) {
            $transaction->rollBack();
            return new FuncResult(1, $e->getMessage());
        }
    }
    /**
     * 投票劵投票
     *
     * @param BUser $userModel
     * @param array $data
     * @return void
     */
    public static function voucherVote(BUser $userModel, array $data = [])
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $voteModel = new BVote();
            $voteModel->node_id = $data['node_id'];
            $voteModel->type = BVote::TYPE_VOUCHER;
            $voteModel->status = BVote::STATUS_ACTIVE;
            $voteModel->vote_number = $data['vote_number'];
            $voteModel->create_time = NOW_TIME;
            $voteModel->user_id = $userModel->id;
            if (!$voteModel->save()) {
                throw new ErrorException('投票失败', $voteModel->getFirstError());
            }
            // 插入投票劵使用详情
            $voucherDetailModel = new BVoucherDetail();
            $voucherDetailModel->user_id = $userModel->id;
            $voucherDetailModel->node_id = $data['node_id'];
            $voucherDetailModel->vote_id = $voteModel->id;
            $voucherDetailModel->amount = $data['vote_number'];
            $voucherDetailModel->create_time = NOW_TIME;
            if (!$voucherDetailModel->save()) {
            throw new ErrorException('投票详情插入失败', $voucherDetailModel->getFirstError());
            }
            $transaction->commit();
            return new FuncResult(0, '投票成功');
        } catch (\Exception $e) {
            $transaction->rollBack();
            // var_dump($e->getMessage());exit;
            return new FuncResult(1, '投票失败');
        }
    }

    /**
     * 获取可赎回投票
     *
     * @param integer $userId
     * @return boolean
     */
    public static function getRevokeList(int $userId = 0)
    {
        $voteModel = BVote::find()
        ->active(BVote::STATUS_INACTIVE_ING)
        ->where(['undo_time' => 0, 'type' => BVote::TYPE_ORDINARY]);
        if ($userId) {
            $voteModel->andWhere(['user_id' => $userId]);
        }
        return $voteModel->all();
    }

    /**
     * 赎回操作
     *
     * @param integer $userId
     * @return void
     */
    public static function revokeAction(int $userId, int $voteId)
    {
        if ($userId <= 0) {
            return new FuncResult(1, '输入正确用户ID');
        }
        $frozenModel = BUserCurrencyFrozen::find()
        ->active(BUserCurrencyFrozen::STATUS_FROZEN)
        ->where(['type' => BUserCurrencyFrozen::$TYPE_VOTE, 'relate_id' => $voteId])
        ->one();
        if (is_null($frozenModel)) {
            return new FuncResult(1, '未查询到冻结信息');
        }
        $revokeData['id'] = $voteId;
        $revokeData['user_id'] = $userId;
        $revokeData['currency_id'] = $frozenModel->currency_id;
        $revokeData['amount'] = $frozenModel->amount;
        $revokeData['relate_table'] = $frozenModel->relate_table;
        return WithdrawService::withdrawRevokeVote($revokeData);
    }

}
