<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/29 0029
 * Time: 19:35
 */

namespace common\services;

use yii\db\Exception;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use common\components\FuncHelper;
use common\components\FuncResult;
use common\models\business\BUser;
use common\models\business\BUserLog;
use common\models\business\BCurrency;
use common\models\business\BWalletSent;
use common\models\business\BUserIdentify;
use common\models\business\BUserCurrencyDetail;
use common\models\business\BUserCurrencyFrozen;
use common\models\business\BUserRechargeAddress;
use common\models\business\BUserWithdrawAddress;
use common\models\business\BUserRechargeWithdraw;

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
        try {
            // 添加充提币申请
            $withdraw = new BUserRechargeWithdraw();
            $withdraw->setAttributes($data);
            $withdraw->order_number = FuncHelper::generateOrderCode(); // 订单号
            $withdraw->type = BUserRechargeWithdraw::$TYPE_WITHDRAW; // 提币
            $sign = $withdraw->save();
            if (!$sign) {
                throw new ErrorException('user_recharge_withdraw table data is not inserted successfully');
            }
            $withdrawLastId = $withdraw->id;

            // 冻结用户资金
            $userFrozen = new BUserCurrencyFrozen();
            $userFrozen->user_id = $data['user_id'];
            $userFrozen->currency_id = $data['currency_id'];
            $userFrozen->type = BUserCurrencyFrozen::$TYPE_WITHDRAW; // 充提币
            $userFrozen->relate_table = 'user_recharge_withdraw';
            $userFrozen->relate_id = $withdrawLastId;
            $userFrozen->amount = round($data['amount'] + $data['poundage'], 8); // 总数量=提币数量+手续费
            $userFrozen->remark = '提币';
            $userFrozen->status = BUserCurrencyFrozen::STATUS_FROZEN; // 冻结
            $userFrozen->create_time = $data['create_time'];
            $userFrozen->update_time = $data['update_time'];
            $sign = $userFrozen->save();
            if (!$sign) {
                throw new ErrorException('user_currency_frozen table data is not inserted successfully');
            }

            // 重算用户持仓
            $sign = UserService::resetCurrency($data['user_id'], $data['currency_id']);
            if ($sign === false) {
                throw new ErrorException('reset user position fail');
            }

            // 提交
            $transaction->commit();

            return new FuncResult(0, '提交成功', $withdrawLastId);
        } catch (Exception $e) {
            // 回滚
            $transaction->rollBack();

            return new FuncResult(1, '提交失败');
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
    public static function withdrawCurrencyAudit($id, $status, $remark='', $adminId='')
    {
        // 判断当前状态是否可以更改
        $res = BUserRechargeWithdraw::findOne($id);
        if ($res['status'] !== BUserRechargeWithdraw::$STATUS_EFFECT_WAIT) {
            return new FuncResult(1, '当前状态不可更改');
        }

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $adminId = $adminId === '' ? \Yii::$app->user->id : intval($adminId);
            $time = time();

            // 修改user-recharge-withdraw状态
            $sign = BUserRechargeWithdraw::updateAll(
                ['status' => $status, 'status_remark' => $remark, 'audit_admin_id' => $adminId, 'update_time' => $time, 'audit_time' => $time],
                ['=', 'id', $id]
            );
            if ($sign === 0) {
                throw new ErrorException('user-recharge-withdraw table data update is fail');
            }

            // 解冻
            $sign = BUserCurrencyFrozen::updateAll(
                ['status' => BUserCurrencyFrozen::STATUS_THAW, 'update_time' => $time, 'unfrozen_time' => $time],
                ['user_id' => $res['user_id'], 'currency_id' => $res['currency_id'], 'type' => BUserCurrencyFrozen::$TYPE_WITHDRAW, 'relate_id' => $res['id'], 'status' => BUserCurrencyFrozen::STATUS_FROZEN]
            );
            if ($sign === 0) {
                throw new ErrorException('user-currency-frozen table data update is fail');
            }

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
                $currencyDetail->type = BUserCurrencyDetail::$TYPE_WITHDRAW; // 充值提币
                $currencyDetail->status = BUserCurrencyDetail::$STATUS_EFFECT_SUCCESS;
                $currencyDetail->effect_time = $time;
                $currencyDetail->remark = '提币';
                $currencyDetail->amount = -$res['amount'];
                $sign = $currencyDetail->save();
                if (!$sign) {
                    throw new ErrorException('user-currency-detail table data create is fail');
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

                //执行钱包提币
                $currencyJingtum = BCurrency::getJingtumCurrency();

                if (in_array($res['currency_id'], $currencyJingtum)) {
                    $transactionNumber = FuncHelper::generateWalletSentTransNum();
                    $amount = $res['amount'];
                    $jingtumAddress = $res['destination_address'];
                    $walletSentRemark = '';
                    $currency = BCurrency::find()->where(['id' => $res['currency_id']])->limit(1)->one();
                    if (!$currency) {
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
                    if ($sign) {
                        //账户余额是否足够
                        $mainBalanceRes = JingTumService::getInstance()->mainBalance(strtoupper($currency->code));
                        if ($mainBalanceRes->code == 0 && !empty($mainBalanceRes->content)) {
                            $mainBalance = $mainBalanceRes->content;
                        } else {
                            $mainBalance = 0;
                        }
                        if (round($mainBalance - $amount, 8) < 0) {
                            throw new ErrorException('wallet no amount');
                        }

                        $resWalletSent = JingTumService::getInstance()->addUserBalanceFormMain($jingtumAddress, $transactionNumber, $amount, $walletSentRemark, strtoupper($currency->code));
                        if ($resWalletSent->code == 0) {
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
                }
            }

            if (!empty($transactionId)) {
                // 修改提现订单transactionId
                $sign = BUserRechargeWithdraw::updateAll(
                    ['source_address' => \Yii::$app->params['JTAddress'], 'transaction_id' => $transactionId, 'update_time' => $time],
                    ['=', 'id', $id]
                );
                if ($sign === 0) {
                    throw new ErrorException('user-recharge-withdraw table data update is fail');
                }
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
     * @param $res
     * @param $hasFrozen
     * @return array
     * @throws \yii\db\Exception
     * info : 前台货币消费（投票）/ 两种类型 （直接划币|冻结划币）
     */
    public static function withdrawCurrencyVote(array $res, bool $isFrozen = true)
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
            if ($isFrozen) {
                // 冻结用户资金
                $userFrozen = new BUserCurrencyFrozen();
                $userFrozen->setAttributes($currencyData);
                $userFrozen->type = BUserCurrencyFrozen::$TYPE_VOTE; // 投票
                $userFrozen->amount = round($data['amount'], 8); // 总数量
                $userFrozen->remark = BUserCurrencyFrozen::getType($currencyDetail->status) ?? '投票';
                $userFrozen->status = BUserCurrencyFrozen::STATUS_FROZEN; // 冻结
                $sign = $userFrozen->save();
                if (!$sign) {throw new ErrorException('user_currency_frozen table data is not inserted successfully');}
            } else {
                // 投票明细
                $currencyDetail = new BUserCurrencyDetail();
                $currencyDetail->setAttributes($currencyData);
                $currencyDetail->type = BUserCurrencyDetail::$TYPE_VOTE; // 投票消费
                $currencyDetail->status = BUserCurrencyDetail::$STATUS_EFFECT_SUCCESS;
                $currencyDetail->effect_time = $time;
                $currencyDetail->remark = BUserCurrencyDetail::getType($currencyDetail->status) ?? '投票';
                $currencyDetail->amount = -$res['amount'];
                $sign = $currencyDetail->save();
                if (!$sign) { throw new ErrorException('user-currency-detail table data create is fail'); }
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

            //执行钱包币种ID
            $currencyJingtum = BCurrency::getJingtumCurrency();

            if (in_array($res['currency_id'], $currencyJingtum)) {
                $transactionNumber = FuncHelper::generateWalletSentTransNum();
                $amount = $res['amount'];
                $jingtumAddress = $res['source_address'];
                // 用户私钥地址
                $privateKey = $res['privateKey'];
                $walletSentRemark = '';
                $currency = BCurrency::find()->where(['id' => $res['currency_id']])->limit(1)->one();
                if (!$currency) {
                    throw new ErrorException('no currency code');
                }

                $walletSent = new BWalletSent();
                $walletSent->currency_id = $res['currency_id'];
                $walletSent->transaction_number = $transactionNumber;
                $walletSent->type = BWalletSent::$TYPE_WITHDRAW;
                $walletSent->relate_table = $relate_table;
                $walletSent->relate_id = $res['id'];
                $walletSent->amount = $amount;
                $walletSent->source_address = $jingtumAddress;
                $walletSent->destination_address = \Yii::$app->params['JTAddress'];
                $walletSent->remark = $walletSentRemark;
                $walletSent->status = BWalletSent::$STATUS_WAIT;
                $walletSent->create_time = $time;
                $walletSent->update_time = $time;
                $sign = $walletSent->insert();
                $walletSentId = $walletSent->id;
                if ($sign) {
                    $resWalletSent = JingTumService::getInstance()->addMainBalanceFormUser($privateKey, $jingtumAddress, $transactionNumber, $amount, $walletSentRemark, strtoupper($currency->code));
                    if ($resWalletSent->code == 0) {
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
     * 投票赎回
     *
     * @param array $revoke
     * @return void
     */
    public static function withdrawRevokeVote(array $res)
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $time = time();
            $relate_table = isset($res['relate_table']) ? $res['relate_table'] : 'vote';
            // 解冻
            $sign = BUserCurrencyFrozen::updateAll(
                ['status' => BUserCurrencyFrozen::STATUS_THAW, 'update_time' => $time, 'unfrozen_time' => $time],
                ['user_id' => $res['user_id'], 'currency_id' => $res['currency_id'], 'type' => BUserCurrencyFrozen::$TYPE_VOTE, 'relate_id' => $res['id'], 'status' => BUserCurrencyFrozen::STATUS_FROZEN]
            );
            if ($sign === 0) {
                throw new ErrorException('user-currency-frozen table data update is fail');
            }
            //执行钱包币种ID
            $currencyJingtum = BCurrency::getJingtumCurrency();

            if (in_array($res['currency_id'], $currencyJingtum)) {
                $transactionNumber = FuncHelper::generateWalletSentTransNum();
                $amount = $res['amount'];
                if (isset($res['destination_address'])) {
                    $jingtumAddress = $res['destination_address'];
                } else {
                    $userAddressModel = BUserRechargeAddress::find()->where(['user_id' => $res['user_id'], 'currency_id' => $res['currency_id']])->limit(1)->one();
                    if (is_null($userAddressModel)) {
                        throw new ErrorException('user address no found');
                    }
                    $jingtumAddress = $userAddressModel->address;
                }
                $walletSentRemark = '';
                $currency = BCurrency::find()->where(['id' => $res['currency_id']])->limit(1)->one();
                if (!$currency) {
                    throw new ErrorException('no currency code');
                }

                $walletSent = new BWalletSent();
                $walletSent->currency_id = $res['currency_id'];
                $walletSent->transaction_number = $transactionNumber;
                $walletSent->type = BWalletSent::$TYPE_WITHDRAW;
                $walletSent->relate_table = $relate_table;
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
                if ($sign) {
                    //账户余额是否足够
                    $mainBalanceRes = JingTumService::getInstance()->mainBalance(strtoupper($currency->code));
                    if ($mainBalanceRes->code == 0 && !empty($mainBalanceRes->content)) {
                        $mainBalance = $mainBalanceRes->content;
                    } else {
                        $mainBalance = 0;
                    }

                    if (round($mainBalance - $amount, 8) < 0) {
                        throw new ErrorException('wallet no amount');
                    }
                    $resWalletSent = JingTumService::getInstance()->addUserBalanceFormMain($jingtumAddress, $transactionNumber, $amount, $walletSentRemark, strtoupper($currency->code));
                    if ($resWalletSent->code == 0) {
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

        $currencyJingtum = BCurrency::getJingtumCurrency();
        if (in_array($currencyId, $currencyJingtum)) {
            $res = JingTumService::getInstance()->queryBalance($address);
            if ($res->code == 0) {
                return true;
            } else {
                return false;
            }
        }

        return true;
    }
}
