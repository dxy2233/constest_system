<?php

namespace common\services;

use yii\base\ErrorException;
use common\components\FuncResult;
use common\models\business\BUser;
use common\models\business\BVote;
use common\models\business\BWalletJingtum;
use common\models\business\BUserCurrencyDetail;

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

    public static function currencyVote(BUser $userModel, array $data = [], int $type = 1)
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $voteModel = new BVote();
            $voteModel->node_id = $data['node_id'];
            $voteModel->type = $type;
            $voteModel->status = BVote::STATUS_ACTIVE;
            $voteModel->vote_number = $data['vote_number'];
            if ($type == BVote::TYPE_PAY) {
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
                'user_recharge' => 'trans_vote',
            ];
            $currencyTrans = WithdrawService::withdrawCurrencyTrans($res);
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
}
