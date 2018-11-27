<?php

namespace console\controllers;

use common\components\FuncHelper;
use common\models\business\BCurrency;
use common\models\business\BReportJingtum;
use common\models\business\BUserRechargeAddress;
use common\models\business\BWalletJingtum;
use common\models\business\BWalletSent;
use common\models\Setting;
use common\services\JingTumService;
use common\services\RechargeService;

class ReportJingtumController extends BaseController
{

    /**
     * 默认
     */
    public function actionIndex()
    {
        echo "welcome";
    }

    /**
     * 获取交易列表
     *
     * @return void
     */
    public function actionGetTrans()
    {
        echo "start".PHP_EOL;
        //井通下的积分
        $currencyJingtum = BCurrency::getJingtumCurrency();

        $jingtumAddressList = BUserRechargeAddress::find()
            ->select(['address'])
            ->distinct()
            ->where(['in', 'currency_id', $currencyJingtum])
            ->asArray()
            ->all();
        foreach ($jingtumAddressList as $jingtumAddress) {
            $jingtumAddress = $jingtumAddress['address'];
            $page = 1;
            $pageSize = 10;
            $flag = false; // 这里是执行交易记录拉取不自动更新交易数据
            echo '-----'.$jingtumAddress.'-----';
            $record = JingTumService::getInstance()->pullTransRecord($jingtumAddress, $page, $pageSize, $flag);
//            var_dump($record);
            if ($record['status'] && !empty($record['count'])) {
                echo 'Success'.PHP_EOL;
                foreach ($record['list'] as $list) {
                    echo $list['type'] . '-' . $list['hash'] .'-'. ($list['already'] ? 'already' : 'yes').PHP_EOL;
                }
            } elseif ($record['status']) {
                echo 'Success'.PHP_EOL;
            } else {
                echo 'Fail'.PHP_EOL;
            }
        }
        echo "end".PHP_EOL;
    }

    /**
     * 更新交易数据
     *
     * @return void
     */
    public function actionUpdateTrans()
    {
        echo "start".PHP_EOL;
        $reportList = BReportJingtum::find()
            ->where(['is_update' => BReportJingtum::$IS_UPDATE_YES])
            ->orderBy('date asc')
            ->all();
        if (empty($reportList)) {
            echo "No transaction data".PHP_EOL;
        }
        foreach ($reportList as $report) {
            echo '-----'.$report['type'].'-'.$report['hash'].'-----';
            $reponse = JingTumService::getInstance()->updateTrans($report);
            echo $reponse->msg.PHP_EOL;
        }
        echo "end".PHP_EOL;
    }

    /**
     * 资产转移到主钱包
     */
    public function actionToMainTrans()
    {
        echo "start".PHP_EOL;

        //井通下的积分
        $currencyJingtum = BCurrency::getJingtumCurrency();

        $jingtumAddressList = BUserRechargeAddress::find()
            ->select(['address'])
            ->distinct()
            ->where(['in', 'currency_id', $currencyJingtum])
            ->asArray()
            ->all();

        foreach ($jingtumAddressList as $jingtumAddress) {
            $jingtumAddress = $jingtumAddress['address'];
            echo "report address:" . $jingtumAddress . PHP_EOL;

            $resJingTum = JingTumService::getInstance()->queryBalance($jingtumAddress);
            // var_dump($resJingTum);continue;
            if ($resJingTum->code == 0) {
                foreach ($resJingTum->content as $key => $val) {
                    $currency = BCurrency::find()->where(['code' => strtolower($key)])->one();
                    if (!$currency) {
                        echo "no currency code:" . $key . PHP_EOL;
                        continue;
                    }
                    echo "currency code:" . $key . PHP_EOL;
                    //用户钱包信息
                    $walletJingtum = BWalletJingtum::find()->where(['address' => $jingtumAddress])->one();
                    if (!$walletJingtum) {
                        echo "no walletJingtum address:" . $jingtumAddress . PHP_EOL;
                        continue;
                    }

                    //转移资金
                    $transactionNumber = FuncHelper::generateWalletSentTransNum();
                    //钱包保留GRT激活余额
                    if ($currency->id == BCurrency::getCurrencyIdByCode(BCurrency::$CURRENCY_GRT)) {
                        $amount = round($val['value'] - \Yii::$app->params['JTWalletActiveAmount'], 8);
                    } else {
                        $amount = round($val['value'], 8);
                    }
                    if ($amount <= 0) {
                        echo "no amount:" . $amount . PHP_EOL;
                        continue;
                    }

                    $nowTime = time();
                    $remark = "";
                    //转移资金
                    $walletSent = new BWalletSent();
                    $walletSent->currency_id = $currency->id;
                    $walletSent->transaction_number = $transactionNumber;
                    $walletSent->type = BWalletSent::$TYPE_TO_MAIN;
                    $walletSent->relate_table = '';
                    $walletSent->relate_id = 0;
                    $walletSent->amount = $amount;
                    $walletSent->source_address = $jingtumAddress;
                    $walletSent->destination_address = \Yii::$app->params['JTWallet']['receipt']['address'];
                    $walletSent->remark = $remark;
                    $walletSent->status = BWalletSent::$STATUS_WAIT;
                    $walletSent->create_time = $nowTime;
                    $walletSent->update_time = $nowTime;
                    $res = $walletSent->insert();
                    $walletSentId = $walletSent->id;
                    if ($res) {
                        $resWalletSent = JingTumService::getInstance()->userTransferUser($walletJingtum->secret, $jingtumAddress, \Yii::$app->params['JTWallet']['receipt']['address'], $transactionNumber, $amount, $remark, strtoupper($currency->code));
                        if ($resWalletSent->code == 0) {
                            BWalletSent::updateAll([
                                'transaction_id' => $resWalletSent->content['hash'],
                                'response_data' => json_encode($resWalletSent->content['responseData']),
                                'status' => BWalletSent::$STATUS_SUCCESS,
                                'update_time' => $nowTime,
                            ], ['id' => $walletSentId]);
                            echo "trans success" . PHP_EOL;
                        } else {
                            BWalletSent::updateAll([
                                'response_data' => is_array($resWalletSent->msg) ? json_encode($resWalletSent->msg) : $resWalletSent->msg,
                                'status' => BWalletSent::$STATUS_FAIL,
                                'update_time' => $nowTime,
                            ], ['id' => $walletSentId]);
                            echo "trans fail" . PHP_EOL;
                        }
                    } else {
                        echo "trans insert fail" . PHP_EOL;
                    }
                }
            } else {
                var_dump($resJingTum->msg);
                echo PHP_EOL;
            }
        }

        echo "end".PHP_EOL;
    }
}
