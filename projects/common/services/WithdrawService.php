<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/29 0029
 * Time: 19:35
 */

namespace common\services;

use common\components\FuncHelper;
use common\components\FuncResult;
use common\models\business\BCurrency;
use common\models\business\BUser;
use common\models\business\BUserCurrencyDetail;
use common\models\business\BUserCurrencyFrozen;
use common\models\business\BUserIdentify;
use common\models\business\BUserLog;
use common\models\business\BUserRechargeWithdraw;
use common\models\business\BUserWithdrawAddress;
use common\models\business\BWalletSent;
use yii\base\ErrorException;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

class WithdrawService extends ServiceBase
{
    /**
     * @param $data
     * $data = [ 2018-01-19 18:03
            'currency_id' => $currencyId,
            'user_id' => $this->user_id,
            'type' => BUserRechargeWithdraw::$TYPE_WITHDRAW,
            'amount' => $amount, // 总数量
            'poundage' => $poundage, // 手续费
            'destination_address' => $address, // 接收方地址
            'tag' => $addressTag, // 地址标签
            'remark' => '提币',
            'status' => BUserRechargeWithdraw::$STATUS_EFFECT_WAIT,
            'status_remark' => '待确认',
            'create_time' => $time,
            'update_time' => $time,
        ];
     * @return bool
     * @throws ErrorException
     * @throws Exception
     * @throws \Exception
     * info : 提币申请(前台)
     */
    public static function withdrawCurrencyApply($data)
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try{
            // 添加充提币申请
            $withdraw = new BUserRechargeWithdraw();
            $withdraw->setAttributes($data);
            $withdraw->order_number = FuncHelper::generateOrderCode(); // 订单号
            $withdraw->type = BUserRechargeWithdraw::$TYPE_WITHDRAW; // 提币
            $sign = $withdraw->save();
            if (!$sign) {throw new ErrorException('user_recharge_withdraw table data is not inserted successfully');}
            $withdrawLastId = $withdraw->id;

            // 冻结用户资金
            $userFrozen = new BUserCurrencyFrozen();
            $userFrozen->user_id = $data['user_id'];
            $userFrozen->currency_id = $data['currency_id'];
            $userFrozen->type = BUserCurrencyFrozen::$TYPE_RECHARGE_WITHDRAW; // 充提币
            $userFrozen->relate_table = 'user_recharge_withdraw';
            $userFrozen->relate_id = $withdrawLastId;
            $userFrozen->amount = round($data['amount'] + $data['poundage'], 8); // 总数量=提币数量+手续费
            $userFrozen->remark = '提币';
            $userFrozen->status = BUserCurrencyFrozen::$FROZEN_YES; // 冻结
            $userFrozen->create_time = $data['create_time'];
            $userFrozen->update_time = $data['update_time'];
            $sign = $userFrozen->save();
            if (!$sign) {throw new ErrorException('user_currency_frozen table data is not inserted successfully');}

            // 重算用户持仓
            $sign = UserService::resetCurrency($data['user_id'], $data['currency_id']);
            if ($sign === false) {throw new ErrorException('reset user position fail');}

            // 提交
            $transaction->commit();
            return true;
        } catch (Exception $e) {
            // 回滚
            $transaction->rollBack();
            return false;
        }
    }

    /**
     * @param $id
     * @param $status
     * @param string $remark
     * @return array
     * @throws \yii\db\Exception
     * info : 提币审核（后台）
     */
    public static function withdrawCurrencyCheck($id, $status, $remark='')
    {
        // 判断当前状态是否可以更改
        $res = BUserRechargeWithdraw::findOne($id);
        if ($res['status'] !== BUserRechargeWithdraw::$STATUS_EFFECT_WAIT) {
            return ['msg' => '当前状态不可更改', 'status' => 1];
        }

        $transaction = \Yii::$app->db->beginTransaction();
        try{
            $adminId = \Yii::$app->user->id;
            $time = time();

            // 修改user-recharge-withdraw状态
            $sign = BUserRechargeWithdraw::updateAll(
                ['status' => $status, 'status_remark' => $remark, 'audit_admin_id' => $adminId, 'update_time' => $time, 'audit_time' => $time],
                ['=', 'id', $id]
            );
            if ($sign === 0) { throw new ErrorException('user-recharge-withdraw table data update is fail'); }

            // 解冻
            $sign = BUserCurrencyFrozen::updateAll(
                ['status' => BUserCurrencyFrozen::$FROZEN_NO, 'update_time' => $time, 'unfrozen_time' => $time],
                ['user_id' => $res['user_id'], 'currency_id' => $res['currency_id'], 'type' => BUserCurrencyFrozen::$TYPE_RECHARGE_WITHDRAW, 'relate_id' => $res['id'], 'status' => BUserCurrencyFrozen::$FROZEN_YES]
            );
            if ($sign === 0) { throw new ErrorException('user-currency-frozen table data update is fail'); }

             // 增加明细
            if ($status == BUserRechargeWithdraw::$STATUS_EFFECT_SUCCESS) {
                $currencyData = [
                    'user_id' => $res['user_id'],
                    'currency_id' => $res['currency_id'],
                    'relate_table' => 'user_recharge_withdraw',
                    'relate_id' => $res['id'],
                    'create_time' => $time,
                    'update_time' => $time,
                ];
                // 提币明细
                $currencyDetail = new BUserCurrencyDetail();
                $currencyDetail->setAttributes($currencyData);
                $currencyDetail->type = BUserCurrencyDetail::$TYPE_RECHARGE_WITHDRAW; // 充值提币
                $currencyDetail->status = BUserCurrencyDetail::$STATUS_EFFECT_SUCCESS;
                $currencyDetail->effect_time = $time;
                $currencyDetail->remark = '提币';
                $currencyDetail->amount = -$res['amount'];
                $sign = $currencyDetail->save();
                if (!$sign) { throw new ErrorException('user-currency-detail table data create is fail'); }

                // 手续费明细
                $currencyDetailPoundage = new BUserCurrencyDetail();
                $currencyDetailPoundage->setAttributes($currencyData);
                $currencyDetailPoundage->type = BUserCurrencyDetail::$TYPE_POUNDAGE; // 手续费
                $currencyDetailPoundage->status = BUserCurrencyDetail::$STATUS_EFFECT_SUCCESS;
                $currencyDetailPoundage->effect_time = $time;
                $currencyDetailPoundage->amount = -$res['poundage'];
                $currencyDetailPoundage->remark = '手续费';
                $sign = $currencyDetailPoundage->save();
                if (!$sign) { throw new ErrorException('user-currency-detail table data create is fail'); }

                //执行钱包提币
                $currencyJingtum = BCurrency::getJingtumCurrency();

                if(in_array($res['currency_id'], $currencyJingtum)) {
                    $transactionNumber = FuncHelper::generateWalletSentTransNum();
                    $amount = $res['amount'];
                    $jingtumAddress = $res['destination_address'];
                    $walletSentRemark = '';
                    $currency = BCurrency::find()->where(['id' => $res['currency_id']])->limit(1)->one();
                    if(!$currency) {
                        throw new ErrorException('no currency code');
                    }

                    $walletSent = new BWalletSent();
                    $walletSent->currency_id = $res['currency_id'];
                    $walletSent->transaction_number = $transactionNumber;
                    $walletSent->type = BWalletSent::$TYPE_WITHDRAW;
                    $walletSent->relate_table = 'user_recharge_withdraw';
                    $walletSent->relate_id = $res['id'];
                    $walletSent->amount = $amount;
                    $walletSent->source_address = \Yii::$app->params['JTAddress'];
                    $walletSent->destination_address = $jingtumAddress;
                    $walletSent->remark = $walletSentRemark;
                    $walletSent->status = BWalletSent::$STATUS_WAIT;
                    $walletSent->create_time = $time;
                    $walletSent->update_time = $time;
                    $sign = $walletSent->insert();
                    $walletSentId = $walletSent->id;
                    if($sign) {
                        //账户余额是否足够
                        $mainBalanceRes = JingTumService::getInstance()->mainBalance(strtoupper($currency->code));
                        if($mainBalanceRes->code == 0 && !empty($mainBalanceRes->content)) {
                            $mainBalance = $mainBalanceRes->content;
                        } else {
                            $mainBalance = 0;
                        }
                        if(round($mainBalance - $amount, 8) < 0) {
                            throw new ErrorException('wallet no amount');
                        }

                        $resWalletSent = JingTumService::getInstance()->addUserBalanceFormMain($jingtumAddress,$transactionNumber,$amount,$walletSentRemark,strtoupper($currency->code));
                        if($resWalletSent->code == 0) {
                            $transactionId = $resWalletSent->content['hash'];
                            BWalletSent::updateAll([
                                'transaction_id' => $resWalletSent->content['hash'],
                                'response_data' => json_encode($resWalletSent->content['responseData']),
                                'status' => BWalletSent::$STATUS_SUCCESS,
                                'update_time' => $time,
                            ], ['id' => $walletSentId]);
                        } else {
                            //todo 在事务里将不会记录失败的记录
                            throw new ErrorException('wallet trans fail');
                        }
                    } else {
                        throw new ErrorException('trans insert fail');
                    }

                } elseif ($res['currency_id'] == BCurrency::getCurrencyIdByCode(BCurrency::$CURRENCY_BTC)) {
                    // btc处理
                    // btc提币确认数验证
                    $btcWithdrawStatus = CurrencyBtcService::getInstance()->withdrawBtcStatus($res['user_id'], $res['currency_id']);
                    if ($btcWithdrawStatus->code != 0) {
                        // 不能提币
                        throw new ErrorException('withdraw_confirmation not fetch');
                    }

                    $amount = $res['amount'];
                    $btcAddress = $res['destination_address'];
                    $currency = BCurrency::find()->where(['id' => $res['currency_id']])->limit(1)->one();
                    if (!$currency) {
                        throw new ErrorException('no currency code');
                    }

                    // 添加一条货币发送记录
                    $transactionNumber = FuncHelper::generateWalletSentTransNum();
                    $walletSent = new BWalletSent();
                    $walletSent->currency_id = $res['currency_id'];
                    $walletSent->transaction_number = $transactionNumber;
                    $walletSent->type = BWalletSent::$TYPE_WITHDRAW;
                    $walletSent->relate_table = 'user_recharge_withdraw';
                    $walletSent->relate_id = $res['id'];
                    $walletSent->amount = $amount;
                    $walletSent->source_address = ''; //todo: 交易记录查询未直接返回发送方地址，暂不做填写
                    $walletSent->destination_address = $btcAddress;
                    $walletSent->remark = '';
                    $walletSent->status = BWalletSent::$STATUS_WAIT;
                    $walletSent->create_time = $time;
                    $walletSent->update_time = $time;
                    $sign = $walletSent->insert();
                    $walletSentId = $walletSent->id;
                    if($sign) {
                        //账户余额是否足够
                        $btcBalance = CurrencyBtcService::getInstance()->getMainBalance();
                        if ($btcBalance != 0) {
                            // 有余额
                            $mainBalance = $btcBalance;
                        } else {
                            $mainBalance = 0;
                        }

                        if (round($mainBalance - $amount - (\Yii::$app->params['currencyRpc']['btc']['fee'] * 10), 8) < 0) {
                            throw new ErrorException('wallet no amount');
                        }

                        // 转账
                        $sendTransStatus = CurrencyBtcService::getInstance()->sendTransaction($btcAddress, (string)$amount);
                        if ($sendTransStatus->code === 0) {
                            // 资金转移成功
                            BWalletSent::updateAll([
                                'transaction_id' => $sendTransStatus->content['hash'],
                                'response_data' => json_encode($sendTransStatus->content['hash']),
                                'status' => BWalletSent::$STATUS_SUCCESS,
                                'update_time' => $time,
                            ], ['id' => $walletSentId]);
                        } else {
                            //todo 在事务里将不会记录失败的记录
                            throw new ErrorException($sendTransStatus->msg);
                        }

                    } else {
                        throw new ErrorException('trans insert fail');
                    }

                } elseif ($res['currency_id'] == BCurrency::getCurrencyIdByCode(BCurrency::$CURRENCY_ETH)) {
                    // eth处理
                    // eth提币确认数验证
                    $ethWithdrawStatus = CurrencyEthService::getInstance()->withdrawEthStatus($res['user_id'], $res['currency_id']);
                    if ($ethWithdrawStatus->code != 0) {
                        // 不能提币
                        throw new ErrorException('withdraw_confirmation not fetch');
                    }
                    $amount = $res['amount'];
                    $ethAddress = $res['destination_address'];
                    $currency = BCurrency::find()->where(['id' => $res['currency_id']])->limit(1)->one();
                    if (!$currency) {
                        throw new ErrorException('no currency code');
                    }

                    // 添加一条货币发送记录
                    $ethMainAccount = \Yii::$app->params['currencyRpc']['eth']['ethAddress'];
                    $transactionNumber = FuncHelper::generateWalletSentTransNum();

                    $walletSent = new BWalletSent();
                    $walletSent->currency_id = $res['currency_id'];
                    $walletSent->transaction_number = $transactionNumber;
                    $walletSent->type = BWalletSent::$TYPE_WITHDRAW;
                    $walletSent->relate_table = 'user_recharge_withdraw';
                    $walletSent->relate_id = $res['id'];
                    $walletSent->amount = $amount;
                    $walletSent->source_address = $ethMainAccount;
                    $walletSent->destination_address = $ethAddress;
                    $walletSent->remark = '';
                    $walletSent->status = BWalletSent::$STATUS_WAIT;
                    $walletSent->create_time = $time;
                    $walletSent->update_time = $time;
                    $sign = $walletSent->insert();
                    $walletSentId = $walletSent->id;
                    if($sign) {
                        //账户余额是否足够
                        $ethBalance = CurrencyEthService::getInstance()->getCurrentBalance($ethMainAccount);// 返回10进制
                        if ($ethBalance !== '' && $ethBalance != 0) {
                            // 有余额
                            // 计算该笔交易的手续费
                            $ethPoundage = CurrencyEthService::getInstance()->countPoundage($ethMainAccount, $ethAddress, '0x' . dechex($ethBalance));// 返回10进制
                            if (empty($ethPoundage)) {
                                throw new ErrorException('poundage count fail,Maybe the eth address doesn\'t exist ');
                            }
                            $mainBalance = CurrencyEthService::getInstance()->weiTransformationEth($ethBalance - $ethPoundage['poundage']); // wei兑换为eth;
                        } else {
                            $mainBalance = 0;
                        }
                        if (round($mainBalance - $amount, 8) < 0) {
                            throw new ErrorException('wallet no amount');
                        }
                        $amount = CurrencyEthService::getInstance()->ethTransformationWei($amount);
                        $sendTransStatus = CurrencyEthService::getInstance()->sendTransaction($ethMainAccount, $ethAddress, \Yii::$app->params['currencyRpc']['eth']['ethKey'],
                            '0x' . dechex($amount));//不限制gas
                        if ($sendTransStatus->code === 0) {
                            // 资金转移成功
                            BWalletSent::updateAll([
                                'transaction_id' => $sendTransStatus->content['hash'],
                                'response_data' => json_encode($sendTransStatus->content['hash']),
                                'status' => BWalletSent::$STATUS_SUCCESS,
                                'update_time' => $time,
                            ], ['id' => $walletSentId]);
                        } else {
                            //todo 在事务里将不会记录失败的记录
                            throw new ErrorException('wallet trans fail');
                        }
                    } else {
                        throw new ErrorException('trans insert fail');
                    }
                }
            }

            if(!empty($transactionId)) {
                // 修改提现订单transactionId
                $sign = BUserRechargeWithdraw::updateAll(
                    ['transaction_id' => $transactionId, 'update_time' => $time],
                    ['=', 'id', $id]
                );
                if ($sign === 0) { throw new ErrorException('user-recharge-withdraw table data update is fail'); }
            }

            // 重算用户持仓
            $sign = UserService::resetCurrency($res['user_id'], $res['currency_id']);
            if ($sign === false) {throw new ErrorException('reset user position fail');}

            $transaction->commit();
            return $msg = [ 'msg' => '状态更改成功', 'status' => 0];
        } catch (\Exception $e) {

            $transaction->rollBack();
//            return $msg = [ 'msg' => '状态更改失败', 'status' => 1];
            return $msg = [ 'msg' => $e->getMessage(), 'status' => 1];
        }
    }

    /**
     * @param $addressId
     * @param $userId
     * @return bool
     * info: 删除用户提币地址
     */
    public static function deleteWithdrawAddress($addressId, $userId)
    {
        if (empty($addressId) || $addressId == 0) {
            return false;
        }

        $sign = BUserWithdrawAddress::updateAll(
            ['status' => BUserWithdrawAddress::$STATUS_DELETE, 'update_time' => time()],
            ['id' => $addressId, 'user_id' => $userId]
        );
        if (!$sign) {
            return false;
        }

        return true;
    }

    /**
     * @param $currency
     * @param $userId
     * @param $address
     * @param $remark
     * @return bool
     * info : 添加用户提币地址
     */
    public static function addWithdrawAddress($currency, $userId, $address, $remark)
    {
        $time = time();
        $withdrawAddress = new BUserWithdrawAddress();
        $withdrawAddress->currency_id = $currency;
        $withdrawAddress->user_id = $userId;
        $withdrawAddress->address = $address;
        $withdrawAddress->remark = $remark;
        $withdrawAddress->create_time = $time;
        $withdrawAddress->update_time = $time;
        $sign = $withdrawAddress->save();
        if (!$sign) {
            return false;
        }

        return true;
    }

    /**
     * @param $userId
     * @return FuncResult
     * info:用户是否有修改密码等限制24小时内不允许提现记录
     */
    public static function withdrawRestrain($userId)
    {
        $count = BUserLog::find()->where(['user_id' => $userId])->andWhere(['>', 'create_time', NOW_TIME - 24 * 3600])
            ->andWhere(['in', 'type', [BUserLog::$TYPE_ALERT_LOGIN_PWD, BUserLog::$TYPE_ALERT_TRANS_PWD, BUserLog::$TYPE_ALERT_MOBILE]])->count();

        if ($count > 0) {
            return new FuncResult(1, '由于您修改了重要信息，24小时内无法进行提币操作');
        }

        return new FuncResult(0);
    }

    /**
     * @param $userId
     * @param $currencyId
     * @return float|int|mixed
     * info:用户提现额度限制
     */
    public static function getWithdrawMaxAmount($userId, $currencyId)
    {
        if (empty($userId) || empty($currencyId)) {
            return 0.00;
        }

        $currencyArr = BCurrency::find()->where(['id' => $currencyId])->select('withdraw_max_amount')->limit(1)->asArray()->one();
        if (empty($currencyArr) || empty($currencyArr['withdraw_max_amount'])) {
            return 0.00;
        }

        $userIdentityArr = BUser::find()->where(['id' => $userId])->select('is_identified')->asArray()->limit(1)->one();
        if (empty($userIdentityArr)) {
            return 0.00;
        }

        // 获取用户安全验证项
        $userVerify = UserService::safetyVerifyPattern($userId);

        $currencyJsonArr = json_decode($currencyArr['withdraw_max_amount'], true);


        // 用户实名认证增加额度+另外一种或多种安全验证 提现额度变为2级
        if ($userIdentityArr['is_identified'] == 1 && $userVerify['count'] >= 2) {
            return $currencyJsonArr[2];
        }

        // 否则默认额度
        return $currencyJsonArr[1];
    }


    /**
     * @param $address
     * @param $currencyId
     * @return bool
     * info : 验证提币地址合法性
     */
    public static function withdrawAddressCheck($address, $currencyId)
    {
        if (empty($address) || empty($currencyId)) {
            return false;
        }

        $currencyRes = BCurrency::getCurrencyInfoById($currencyId);
        if (empty($currencyRes)) {
            return false;
        }
        switch ($currencyRes->code) {
            case BCurrency::$CURRENCY_ETH:
                if (!(preg_match('/^(0x)?[0-9a-fA-F]{40}$/', $address))) {
                    return false;
                }
                break;
            case BCurrency::$CURRENCY_BTC:
                if (!(preg_match('/^(1|3)[a-zA-Z\d]{24,33}$/', $address)
                    && preg_match('/^[^0OlI]{25,34}$/', $address))) {
                    return false;
                }
                break;
            default:break;
        }

        return true;
    }

}