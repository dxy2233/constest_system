<?php

namespace common\services;

use common\components\FuncHelper;
use common\models\business\BCurrency;
use common\models\business\BSetting;
use common\models\business\BUserCurrencyDetail;
use common\models\business\BUserRechargeAddress;
use common\models\business\BUserRechargeWithdraw;
use common\models\business\BWalletEth;
use common\models\business\BWalletJingtum;
use common\models\business\BWalletSent;
use common\models\Setting;
use common\services\ReturnInfo;

class RechargeService extends ServiceBase {

    /**
     * 获取提现地址
     */
    public static function getAddress($currencyId, $userId) {
        $address = "";

        $rechargeAddress = BUserRechargeAddress::find()
            ->where(['currency_id' => $currencyId,'user_id' => $userId])
            ->limit(1)
            ->one();
        if($rechargeAddress) {
            $address = $rechargeAddress->address;
        } else {
            //井通下的货币
            $currencyJingtum = BCurrency::getJingtumCurrency();
            //井通下的货币共用一个钱包
            if(in_array($currencyId, $currencyJingtum)) {
                $jingtumAddress = BUserRechargeAddress::find()
                    ->where(['user_id' => $userId])
                    ->andWhere(['in', 'currency_id', $currencyJingtum])
                    ->one();
                if($jingtumAddress) {
                    $address = $jingtumAddress->address;
                }
            }

            if(empty($address)) {
                //创建钱包
                $returnInfo = self::createAddress($currencyId);
                if($returnInfo->code) {
                    return new ReturnInfo(1, $returnInfo->msg);
                }
                $address = $returnInfo->content;
            }

            if($address) {
                //添加地址
                $userRechargeAddress = new BUserRechargeAddress();
                $userRechargeAddress->user_id = $userId;
                $userRechargeAddress->currency_id = $currencyId;
                $userRechargeAddress->address = $address;
                $userRechargeAddress->create_time = NOW_TIME;
                $userRechargeAddress->update_time = NOW_TIME;
                $res = $userRechargeAddress->insert();
                if(!$res) {
                    return new ReturnInfo(1, "获取失败");
                }

                //井通下的货币共用一个钱包，添加相应货币地址
                if(in_array($currencyId, $currencyJingtum)) {
                    foreach($currencyJingtum AS $val) {
                        $rechargeAddressJingtum = BUserRechargeAddress::find()
                            ->where(['currency_id' => $val,'user_id' => $userId])
                            ->one();
                        if(empty($rechargeAddressJingtum)) {
                            $userRechargeAddress = new BUserRechargeAddress();
                            $userRechargeAddress->user_id = $userId;
                            $userRechargeAddress->currency_id = $val;
                            $userRechargeAddress->address = $address;
                            $userRechargeAddress->create_time = NOW_TIME;
                            $userRechargeAddress->update_time = NOW_TIME;
                            $userRechargeAddress->insert();
                        }
                    }
                }
            }
        }

        return new ReturnInfo(0, "获取成功", [
            'address' => $address
        ]);
    }

    /**
     * 生成货币地址
     * @param $currencyId
     * @return string
     */
    private static function createAddress($currencyId) {
        $address = "";

        $currencyJingtum = BCurrency::getJingtumCurrency();

        if(in_array($currencyId, $currencyJingtum)) {
            //账户余额是否足够
            $amount = \Yii::$app->params['JTWalletActiveAmount'];
            $mainBalanceRes = JingTumService::getInstance()->queryBalance(\Yii::$app->params['JTWallet']['active']['address'], JingTumService::ASSETS_TYPE_GRT);
            if ($mainBalanceRes->code == 0 && !empty($mainBalanceRes->content)) {
                $mainBalance = $mainBalanceRes->content;
            } else {
                $mainBalance = 0;
            }
            if (round($mainBalance - $amount, 8) >= 0) {
                $resWallet = JingTumService::getInstance()->createNewWallet();
                if ($resWallet->code == 0) {
                    //添加钱包
                    $walletJingtum = new BWalletJingtum();
                    $walletJingtum->secret = $resWallet->content['secret'];
                    $walletJingtum->address = $resWallet->content['address'];
                    $walletJingtum->create_time = NOW_TIME;
                    $res = $walletJingtum->insert();
                    if ($res) {
                        //成功
                        $address = $resWallet->content['address'];
                    }

                    if ($address) {
                        $walletJingtumId = $walletJingtum->id;
                        $transactionNumber = FuncHelper::generateWalletSentTransNum();
                        $amount = \Yii::$app->params['JTWalletActiveAmount'];
                        $remark = "";
                        //激活钱包
                        $walletSent = new BWalletSent();
                        $walletSent->currency_id = BCurrency::getCurrencyIdByCode(BCurrency::$CURRENCY_GRT);
                        $walletSent->transaction_number = $transactionNumber;
                        $walletSent->type = BWalletSent::$TYPE_ACTIVE;
                        $walletSent->relate_table = 'wallet_jingtum';
                        $walletSent->relate_id = $walletJingtumId;
                        $walletSent->amount = $amount;
                        $walletSent->source_address = \Yii::$app->params['JTWallet']['active']['address'];
                        $walletSent->destination_address = $address;
                        $walletSent->remark = $remark;
                        $walletSent->status = BWalletSent::$STATUS_WAIT;
                        $walletSent->create_time = NOW_TIME;
                        $walletSent->update_time = NOW_TIME;
                        $res = $walletSent->insert();
                        $walletSentId = $walletSent->id;

                        if ($res) {
                            $resWalletSent = JingTumService::getInstance()->userTransferUser(\Yii::$app->params['JTWallet']['active']['key'], \Yii::$app->params['JTWallet']['active']['address'], $address, $transactionNumber, $amount, $remark, JingTumService::ASSETS_TYPE_GRT);
                            if ($resWalletSent->code == 0) {
                                BWalletSent::updateAll([
                                    'transaction_id' => $resWalletSent->content['hash'],
                                    'response_data' => json_encode($resWalletSent->content['responseData']),
                                    'status' => BWalletSent::$STATUS_SUCCESS,
                                    'update_time' => NOW_TIME,
                                ], ['id' => $walletSentId]);
                            } else {
                                BWalletSent::updateAll([
                                    'response_data' => is_array($resWalletSent->msg) ? json_encode($resWalletSent->msg) : $resWalletSent->msg,
                                    'status' => BWalletSent::$STATUS_FAIL,
                                    'update_time' => NOW_TIME,
                                ], ['id' => $walletSentId]);
                                $address = "";
                            }
                        } else {
                            $address = "";
                        }
                    }
                } else {
                    $address = "";
                }
            }
        } else {
            $address = "";
        }

        if($address) {
            return new ReturnInfo(0, "生成成功", $address);
        } else {
            return new ReturnInfo(1, "生成失败");
        }
    }

    /**
     * 充币操作
     */
    public static function handle($currencyId, $address, $tag = "", $amount, $transaction_id = "", $source_address = "") {
        $userRechargeAddress = BUserRechargeAddress::find()->where(['currency_id' => $currencyId, 'address' => $address])->one();
        if($transaction_id) {
            $userRechargeWithdrawOne = BUserRechargeWithdraw::find()->where(['currency_id' => $currencyId, 'transaction_id' => $transaction_id, 'type' => BUserRechargeWithdraw::$TYPE_RECHARGE])->one();
            if($userRechargeWithdrawOne) {
                //交易ID已存在，不再充值
                return new ReturnInfo(2, "transaction_id error");
            }
        }
        if($userRechargeAddress) {
            //事务开始
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $rechargePoundage = 0;//BSetting::get('recharge_poundage');

                //添加订单
                $newUserRechargeWithdraw = new BUserRechargeWithdraw();
                $newUserRechargeWithdraw->order_number = FuncHelper::generateOrderCode();
                $newUserRechargeWithdraw->currency_id = $currencyId;
                $newUserRechargeWithdraw->user_id = $userRechargeAddress->user_id;
                $newUserRechargeWithdraw->type = BUserRechargeWithdraw::$TYPE_RECHARGE;
                $newUserRechargeWithdraw->amount = $amount;
                $newUserRechargeWithdraw->transaction_id = $transaction_id;
                if($rechargePoundage > 0) {
                    $newUserRechargeWithdraw->poundage = abs(round($amount*$rechargePoundage, 8));
                }
                $newUserRechargeWithdraw->source_address = $source_address;
                $newUserRechargeWithdraw->destination_address = $address;
                $newUserRechargeWithdraw->tag = $tag;
                $newUserRechargeWithdraw->remark = "充币";
                $newUserRechargeWithdraw->status = BUserRechargeWithdraw::$STATUS_EFFECT_SUCCESS;
                $newUserRechargeWithdraw->audit_time = NOW_TIME;
                $newUserRechargeWithdraw->create_time = NOW_TIME;
                $newUserRechargeWithdraw->update_time = NOW_TIME;
                $res = $newUserRechargeWithdraw->insert();
                if(!$res) {
                    throw new \Exception('insert user recharge withdraw fail');
                }
                $userRechargeWithdrawId = $newUserRechargeWithdraw->id;

                //添加货币资金明细
                $newUserCurrencyDetail = new BUserCurrencyDetail();
                $newUserCurrencyDetail->currency_id = $currencyId;
                $newUserCurrencyDetail->user_id = $userRechargeAddress->user_id;
                $newUserCurrencyDetail->type = BUserCurrencyDetail::$TYPE_RECHARGE;
                $newUserCurrencyDetail->relate_table = 'user_recharge_withdraw';
                $newUserCurrencyDetail->relate_id = $userRechargeWithdrawId;
                $newUserCurrencyDetail->amount = $amount;
                $newUserCurrencyDetail->remark = "充币";
                $newUserCurrencyDetail->status = BUserCurrencyDetail::$STATUS_EFFECT_SUCCESS;
                $newUserCurrencyDetail->effect_time = NOW_TIME;
                $newUserCurrencyDetail->create_time = NOW_TIME;
                $newUserCurrencyDetail->update_time = NOW_TIME;
                $res = $newUserCurrencyDetail->insert();
                if(!$res) {
                    throw new \Exception('insert user currency detail fail');
                }

                //添加货币手续费资金明细
                if($rechargePoundage > 0) {
                    $newUserCurrencyDetail = new BUserCurrencyDetail();
                    $newUserCurrencyDetail->currency_id = $currencyId;
                    $newUserCurrencyDetail->user_id = $userRechargeAddress->user_id;
                    $newUserCurrencyDetail->type = BUserCurrencyDetail::$TYPE_POUNDAGE;
                    $newUserCurrencyDetail->relate_table = 'user_recharge_withdraw';
                    $newUserCurrencyDetail->relate_id = $userRechargeWithdrawId;
                    $newUserCurrencyDetail->amount = -abs(round($amount*$rechargePoundage, 8));
                    $newUserCurrencyDetail->remark = "手续费";
                    $newUserCurrencyDetail->status = BUserCurrencyDetail::$STATUS_EFFECT_SUCCESS;
                    $newUserCurrencyDetail->effect_time = NOW_TIME;
                    $newUserCurrencyDetail->create_time = NOW_TIME;
                    $newUserCurrencyDetail->update_time = NOW_TIME;
                    $res = $newUserCurrencyDetail->insert();
                    if(!$res) {
                        throw new \Exception('insert user poundage currency detail fail');
                    }
                }

                //重置用户货币持仓
                $res = UserService::resetCurrency($userRechargeAddress->user_id, $currencyId);
                if(!$res) {
                    throw new \Exception('reset currency fail');
                }


                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
//                echo $e->getMessage();
                return new ReturnInfo(1, "操作失败");
            }
        } else {
            return new ReturnInfo(2, "no user recharge address");
        }

        return new ReturnInfo(0, "操作成功");
    }
}
