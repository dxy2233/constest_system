<?php

namespace common\services;

use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use common\services\UserService;
use common\components\FuncHelper;
use common\components\FuncResult;
use common\models\business\BNode;
use common\models\business\BUser;
use common\models\business\BVote;
use common\models\business\BVoucherDetail;
use common\models\business\BWalletJingtum;
use common\models\business\BUserCurrencyDetail;
use common\models\business\BUserCurrencyFrozen;
use common\models\business\BCurrency;

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

        $historyModelExists = true;

        $data = BCycle::find()->orderBy('id asc')->asArray()->all();
        $bool = false;
        $cycle_end_time = time();
        foreach ($data as $v) {
            if ($v->cycle_start_time <= $vote->create_time && $v->cycle_end_time >= $vote->create_time) {
                $cycle_end_time = $v->cycle_end_time;
                $bool = true;
            }
        }
        
        if ($bool && time() > $cycle_end_time) {
            $historyModelExists = false;
        }

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
            }
            $voteModel->consume = $data['amount'];
            $voteModel->unit_code = $data['unit_code'];
            $voteModel->create_time = NOW_TIME;
            $voteModel->user_id = $userModel->id;
            if (!$voteModel->save()) {
                throw new ErrorException('投票失败', $voteModel->getFirstError());
            }
            $poundage = (int) SettingService::get('vote', 'vote_poundage')->value;
            
            $res = [
                'id' => $voteModel->id,
                'currency_id' => $data['currency_id'],
                'user_id' => $userModel->id,
                'type' => BUserCurrencyDetail::$TYPE_VOTE,
                'amount' => $data['amount'], // 总数量
                'poundage' => $poundage, // 手续费
                'remark' => BUserCurrencyDetail::getType(BUserCurrencyDetail::$TYPE_VOTE),
                'status' => BUserCurrencyDetail::$STATUS_EFFECT_SUCCESS,
                'status_remark' => '确认',
                'create_time' => NOW_TIME,
                'update_time' => NOW_TIME,
                'user_recharge' => $isFrozen ? 'voto_frozen' : 'voto_trans',
            ];
            // 货币投票
            $currencyTrans = self::bankrollVotes($res, $isFrozen);
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
            $voteModel->consume = $data['vote_number'];
            $voteModel->unit_code = $data['unit_code'];
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
            // 重置用户投票券
            if (!UserService::resetVoucher($userModel->id)) {
                throw new ErrorException('投票券资产更新失败');
            }
            $transaction->commit();
            return new FuncResult(0, '投票成功');
        } catch (\Exception $e) {
            $transaction->rollBack();
            // var_dump($e->getMessage());
            return new FuncResult(1, '投票失败');
        }
    }

    /**
     * 获取可赎回投票
     *
     * @param integer $userId
     * @return boolean
     */
    public static function getRevokeList(int $userId = 0, callable $callback = null)
    {
        $voteModel = BVote::find()
        ->active(BVote::STATUS_INACTIVE_ING)
        ->where(['type' => BVote::TYPE_ORDINARY]);
        if ($userId) {
            $voteModel->andWhere(['user_id' => $userId]);
        }
        
        if ($callback !== null && $callback instanceof \Closure) {
            $voteModel = call_user_func($callback, $voteModel);
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
        return self::thawVote($revokeData);
    }

    
    /**
     * @param $res
     * @param $hasFrozen
     * @return array
     * @throws \yii\db\Exception
     * info : 前台货币消费（投票）/ 两种类型
     */
    public static function bankrollVotes(array $res, bool $isFrozen = true)
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $time = time();
            $relate_table = isset($res['relate_table']) ? $res['relate_table'] : 'vote';
            // 增加明细
            $currencyData = [
                'user_id' => $res['user_id'],
                'currency_id' => $res['currency_id'],
                'relate_table' => $relate_table,
                'relate_id' => $res['id'],
                'create_time' => $time,
                'update_time' => $time,
            ];
            $remark = '';
            $amount = 0;
            $type = 0;
            if ($isFrozen) {
                // 冻结用户资金
                $userFrozen = new BUserCurrencyFrozen();
                $userFrozen->setAttributes($currencyData);
                $userFrozen->type = $type = BUserCurrencyFrozen::$TYPE_VOTE; // 投票
                $userFrozen->amount = round($res['amount'], 8); // 总数量
                $userFrozen->remark = $remark = BUserCurrencyFrozen::getType(BUserCurrencyFrozen::$TYPE_VOTE) ?? '投票';
                $userFrozen->status = BUserCurrencyFrozen::STATUS_FROZEN; // 冻结
                $sign = $userFrozen->save();
                if (!$sign) {
                    throw new ErrorException('user_currency_frozen table data is not inserted successfully');
                }
                $ordinaryGdt = (float) SettingService::get('vote', 'ordinary_gdt')->value;
                $giveAmount = round($userFrozen->amount * $ordinaryGdt, 8);
            } else {
                // 投票明细
                $currencyDetail = new BUserCurrencyDetail();
                $currencyDetail->setAttributes($currencyData);
                $currencyDetail->type = $type = BUserCurrencyDetail::$TYPE_VOTE; // 投票消费
                $currencyDetail->status = BUserCurrencyDetail::$STATUS_EFFECT_SUCCESS;
                $currencyDetail->effect_time = $time;
                $currencyDetail->remark = $remark = BUserCurrencyDetail::getType(BUserCurrencyDetail::$TYPE_VOTE) ?? '投票';
                $currencyDetail->amount = -$res['amount'];
                $sign = $currencyDetail->save();
                if (!$sign) {
                    throw new ErrorException('user-currency-detail table data create is fail');
                }
                
                $payGdt = (float) SettingService::get('vote', 'pay_gdt')->value;
                $giveAmount = round($res['amount'] * $payGdt, 8);
            }
            $data = BCycle::find()->where(['>', 'tenure_end_time', time()])->orderBy('id asc')->asArray()->all();
            $bool = false;
            foreach ($data as $v) {
                if ($v->cycle_start_time <= time() && $v->cycle_end_time >= time()) {
                    $bool = true;
                }
            }
            if ($bool) {
                // 赠送GDT
                $giveDate = [
                'user_id' => $res['user_id'],
                'relate_table' => $relate_table,
                'relate_id' => $res['id'],
                'type' => BUserCurrencyDetail::$TYPE_REWARD,
                'amount' => $giveAmount,
                'remark' => $remark,
                ];
                $give = self::giveCurrency($giveDate);
                if ($give->code) {
                    throw new ErrorException($give->msg);
                }
            }
            
            // 手续费明细
            if ($res['poundage'] > 0) {
                $currencyDetailPoundage = new BUserCurrencyDetail();
                $currencyDetailPoundage->setAttributes($currencyData);
                $currencyDetailPoundage->type = BUserCurrencyDetail::$TYPE_POUNDAGE; // 手续费
                $currencyDetailPoundage->status = BUserCurrencyDetail::$STATUS_EFFECT_SUCCESS;
                $currencyDetailPoundage->effect_time = $time;
                $currencyDetailPoundage->amount = -$res['poundage'];
                $currencyDetailPoundage->remark = '手续费';
                $sign = $currencyDetailPoundage->save();
                if (!$sign) {
                    throw new ErrorException('user-currency-detail table data create is fail');
                }
            }

            // 重算用户持仓
            $sign = UserService::resetCurrency($res['user_id'], $res['currency_id']);
            if ($sign === false) {
                throw new ErrorException('reset user position fail');
            }
            $sign = UserService::resetCurrency($res['user_id'], BCurrency::getCurrencyIdByCode(BCurrency::$CURRENCY_GDT));
            if ($sign === false) {
                throw new ErrorException('reset user position fail');
            }

            $transaction->commit();

            return new FuncResult(0, '状态更改成功');
        } catch (\Exception $e) {
            $transaction->rollBack();
            // var_dump($e->getMessage());
            return new FuncResult(1, $e->getMessage());
        }
    }

    /**
     * 赎回投票
     *  解冻资产
     * @param array $revoke
     * @return void
     */
    public static function thawVote(array $res)
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $time = time();
            // 解冻
            $sign = BUserCurrencyFrozen::updateAll(
                ['status' => BUserCurrencyFrozen::STATUS_THAW, 'update_time' => $time, 'unfrozen_time' => $time],
                ['user_id' => $res['user_id'], 'currency_id' => $res['currency_id'], 'type' => BUserCurrencyFrozen::$TYPE_VOTE, 'relate_id' => $res['id'], 'status' => BUserCurrencyFrozen::STATUS_FROZEN]
            );
            if ($sign === 0) {
                throw new ErrorException('user-currency-frozen table data update is fail');
            }

            $voteModel = BVote::findOne($res['id']);
            $voteModel->status = BVote::STATUS_INACTIVE;
            if (!$voteModel->save()) {
                throw new ErrorException('vote table data update is fail');
            }

            // 重算用户持仓
            $sign = UserService::resetCurrency($res['user_id'], $res['currency_id']);
            if ($sign === false) {
                throw new ErrorException('reset user position fail');
            }

            $transaction->commit();

            return new FuncResult(0, '状态更改成功');
        } catch (\Exception $e) {
            $transaction->rollBack();
            return new FuncResult(1, $e->getMessage());
        }
    }
    
    /**
     * 赠送GDT
     *
     * @param array $res
     * @return void
     *
     * $res = [
                'user_id' => 用户ID,
                'type' => 类型,
                'relate_table' => 赠送的关系,
                'relate_id' => 赠送关系ID,
                'amount' => 赠送数量,
                'remark' => 赠送备注,
            ];
     */
    public static function giveCurrency(array $res)
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if (!$currencyId = BCurrency::getCurrencyIdByCode(BCurrency::$CURRENCY_GDT)) {
                throw new ErrorException(BCurrency::$CURRENCY_GDT . ' currency undefind');
            }
            // 赠送明细
            $currencyDetail = new BUserCurrencyDetail();
            $currencyDetail->setAttributes($res);
            $currencyDetail->currency_id = $currencyId;
            $currencyDetail->status = BUserCurrencyDetail::$STATUS_EFFECT_SUCCESS;
            $currencyDetail->effect_time = NOW_TIME;
            if (!$currencyDetail->save()) {
                throw new ErrorException('user-currency-detail table data create is fail');
            }
            $transaction->commit();
            return new FuncResult(0, '状态更改成功');
        } catch (\Exception $e) {
            $transaction->rollBack();
            // var_dump($e->getMessage());
            return new FuncResult(1, $e->getMessage());
        }
    }
}
