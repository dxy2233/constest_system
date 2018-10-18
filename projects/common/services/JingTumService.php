<?php
/**
 * Created by PhpStorm.
 * User: havoe
 * Date: 2017/7/18
 * Time: 下午5:19
 */

namespace common\services;

use yii\helpers\ArrayHelper;
use common\services\ServiceBase;
use common\components\FuncHelper;
use common\models\business\BWalletSent;

class JingTumService extends ServiceBase
{

    //贵人通
    const ASSETS_TYPE_GRT = 'GRT';


    protected $api_url;

    public function init()
    {
        parent::init();

        $this->api_url = \Yii::$app->params['JTBusinessUrl'];
    }


    public static function getInstance() : JingTumService
    {
        return parent::getServiceInstance();
    }


    /**
     * 发送请求
     */
    public function send($data, $url = false, $method = false, $header = false)
    {
        $setHeader = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8',
        ];
        if ($header) {
            $setHeader = array_merge($setHeader, $header);
        }
        $httpClient = new \yii\httpclient\Client();
        $method = $method ? $method : 'post';
        $this->api_url = $url ? $url : $this->api_url;
        $sendResult = $httpClient->createRequest()
            ->setHeaders($setHeader)
            ->setMethod($method)
            ->setUrl($this->api_url)
            ->setData($data)
            ->setFormat('json')
            ->send();

        self::info('发起井通请求 - 请求数据 - ' . var_export($data, true) . ' - 返回数据 -' . var_export($sendResult, true), 'service');
        if ($sendResult->isOk) {
            $result = $sendResult->getData();
            if ($result['success']) {
                return new ReturnInfo(0, '请求成功', $result);
            }


            return new ReturnInfo(1, $result, $data);
        }


        return new ReturnInfo(1, '请求失败!', $data);
    }


    /**
     * @desc 获取用户的公钥
     * @param $phone 手机
     * @param $pwd 登陆密码
     * @param $identity 身份证号码
     * @return string
     */
    public function UserPublicKey($phone, $pwd, $identity)
    {
        $secretKey = '8#ONwyJtHesy/WpM';
        $setHeader = [
            'Content-Type' => 'multipart/form-data',
        ];
        $data['phone'] = $phone;
        $data['pwd'] = $pwd;
        $data['identity'] = $identity;
        $data['timestamp'] = '230381199505161220';
        //加密数据
        $encrypt = openssl_encrypt(json_encode($data), 'AES-128-ECB', $secretKey, false);
        $httpClient = new \yii\httpclient\Client();
        $result = $httpClient->createRequest()
            ->setHeaders($setHeader)
            ->setMethod('post')
            ->setData(['data' => $encrypt])
            ->setUrl(\Yii::$app->params['JTAccountUrl'] . '/app/getUserPublicKeyAndSecret')
            ->send();
        LogService::info('发起井通请求 - 请求数据 - ' . var_export(['data' => $data], true) . ' - 返回数据 -' . var_export($result, true), 'jt');
        if ($result->isOk) {
            $result = $result->getData();
            if ($result['success']) {
                //解密数据
                $data = openssl_decrypt($result['data'], 'aes-128-ecb', $secretKey, false);
                $result = json_decode($data, true);
                return new ReturnInfo(0, '请求成功', $result);
            }
            return new ReturnInfo(1, $result);
        }
        return new ReturnInfo(1, '请求失败!');
    }


    /**
     * 账户资产信息
     * @param string $key 公钥
     * @param bool|string $type 资产类型
     * @return ReturnInfo
     */
    public function queryBalance(string $key, string $type = null)
    {
        $result = $this->send([], \Yii::$app->params['JTBusinessUrl'] . '/accounts/' . $key . '/balances', 'get');
        
        if ($result->code === 0) {
            $balances = ArrayHelper::index($result->content['balances'], 'currency');

            if (is_null($type)) {
                return new ReturnInfo(0, '', $balances);
            }

            if (isset($balances[$type])) {
                return new ReturnInfo(0, '', $balances[$type]['value']);
            }

            return new ReturnInfo(1, '不存在的资产类型');
        }

        return $result;
    }


    /**
     * 主账户资产信息
     * @param string|null $type
     * @return ReturnInfo
     */
    public function mainBalance(string $type = null)
    {
        $result = $this->send([], \Yii::$app->params['JTBusinessUrl'] . '/accounts/' . \Yii::$app->params['JTAddress'] . '/balances', 'get');
        if ($result->code === 0) {
            $balances = ArrayHelper::index($result->content['balances'], 'currency');

            if (is_null($type)) {
                return new ReturnInfo(0, '', $balances);
            }

            if (isset($balances[$type])) {
                return new ReturnInfo(0, '', $balances[$type]['value']);
            }

            return new ReturnInfo(1, '不存在的资产类型');
        }
        return $result;
    }


    /**
     * 从公司主账户增加用户钱包资产
     * @param string $userAddress 用户钱包地址
     * @param string $outNo 交易订单号
     * @param float $value 交易额度
     * @param string $remark 备注
     * @param string $type 资产类型
     * @return ReturnInfo
     */
    public function addUserBalanceFormMain(string $userAddress, string $outNo, float $value, string $remark, string $type)
    {
        $issuer = '';
        if ($type != self::ASSETS_TYPE_GRT) {
            $issuer = \Yii::$app->params['JTIssuer'];
        }

        $url = \Yii::$app->params['JTBusinessUrl'] . '/accounts/' . \Yii::$app->params['JTAddress'] . '/payments';
        $data = [
            'secret' => \Yii::$app->params['JTKey'],
            'client_id' => $outNo,
            'payment' => [
                'source' => \Yii::$app->params['JTAddress'],
                'destination' => $userAddress,
                'amount' => [
                    'value' => strval($value),
                    'currency' => $type,
                    'issuer' => $issuer
                ],
                'memos' => [$remark]
            ]
        ];
        $result = $this->send($data, $url);
        if ($result->code === 0) {
            return new ReturnInfo(0, '', [
                'client_id' => $result->content['client_id'],
                'hash' => $result->content['hash'],
                'responseData' => $result->content,
                'requestData'=>$data,
            ]);
        }
        return $result;
    }


    /**
     * 增加主账号资产额度
     * @param string $userPrivateKey 用户私钥
     * @param string $userAddress 用户公钥(钱包地址)
     * @param string $outNo 交易单号
     * @param float $value 交易额度
     * @param string $remark 备注
     * @param string $type 资产类型
     * @return ReturnInfo
     */
    public function addMainBalanceFormUser(string $userPrivateKey, string $userAddress, string $outNo, float $value, string $remark, string $type)
    {
        $issuer = '';
        if ($type !=self::ASSETS_TYPE_GRT) {
            $issuer = \Yii::$app->params['JTIssuer'];
        }

        $url = \Yii::$app->params['JTBusinessUrl'] . '/accounts/' . $userAddress . '/payments';
        $data = [
            'secret' => $userPrivateKey,
            'client_id' => $outNo,
            'payment' => [
                'source' => $userAddress,
                'destination' => \Yii::$app->params['JTAddress'],
                'amount' => [
                    'value' => strval($value),
                    'currency' => $type,
                    'issuer' => $issuer
                ],
                'memos' => [$remark]
            ]
        ];

        $result = $this->send($data, $url);

        if ($result->code === 0) {
            return new ReturnInfo(0, '', [
                'client_id' => $result->content['client_id'],
                'hash' => $result->content['hash'],
                'responseData' => $result->content,
                'requestData'=>$data,
            ]);
        }

        return $result;
    }


    /**
     * 用户之间资产互转
     * @param string $userPrivateKey
     * @param string $userAddress
     * @param string $toUserAddress
     * @param string $outNo
     * @param float $value
     * @param string $remark
     * @param string $type
     * @return ReturnInfo
     */
    public function userTransferUser(
        string $userPrivateKey,
        string $userAddress,
        string $toUserAddress,
        string $outNo,
        float $value,
        string $remark,
        string $type
    ) {
        $issuer = '';
        if ($type != self::ASSETS_TYPE_GRT) {
            $issuer = \Yii::$app->params['JTIssuer'];
        }

        $url = \Yii::$app->params['JTBusinessUrl'] . '/accounts/' . $userAddress . '/payments';
        $data = [
            'secret' => $userPrivateKey,
            'client_id' => $outNo,
            'payment' => [
                'source' => $userAddress,
                'destination' => $toUserAddress,
                'amount' => [
                    'value' => strval($value),
                    'currency' => $type,
                    'issuer' => $issuer
                ],
                'memos' => [$remark]
            ]
        ];

        $result = $this->send($data, $url);

        if ($result->code === 0) {
            return new ReturnInfo(0, '', [
                'client_id' => $result->content['client_id'],
                'hash' => $result->content['hash'],
                'responseData' => $result->content,
                'requestData'=>$data,
            ]);
        }

        return $result;
    }

    /**
     * 创建钱包
     * @param
     */
    public function createNewWallet()
    {
        $result = $this->send([], \Yii::$app->params['JTBusinessUrl'] . '/wallet/new', 'get');
        if ($result->code === 0) {
            return new ReturnInfo(0, '创建成功', $result->content['wallet']);
        }
        return new ReturnInfo(1, '请求失败!');
    }

    /**
     * 初始化一个新账户
     *
     * @return void
     */
    public function initializeWallet(string $address)
    {
        $sourceAddress = \Yii::$app->params['JTAddress'];
        $sourceAmount = \Yii::$app->params['JTWalletActiveAmount'];
        $remark = '钱包激活';
        $transNum = FuncHelper::generateWalletSentTransNum();
        $walletSentModel = new BWalletSent();
        $walletSentModel->transaction_number = $transNum;
        $walletSentModel->type = BWalletSent::$TYPE_ACTIVE;
        $walletSentModel->relate_table = 'wallet_jingtum';
        $walletSentModel->amount = $sourceAmount;
        $walletSentModel->source_address = $sourceAddress;
        $walletSentModel->destination_address = $address;
        $walletSentModel->remark = $remark;
        $walletSentModel->status = BWalletSent::$STATUS_WAIT;
        if (!$walletSentModel->save()) {
            return new ReturnInfo(1, '插入井通激活记录表失败', $walletSentModel->getFirstErrors());
        }
        $resWalletSent = $this->addUserBalanceFormMain($address, $transNum, $sourceAmount, $remark, self::ASSETS_TYPE_GRT);
        
        if ($resWalletSent->code == 0) {
            $resultSent = $resWalletSent->content;
            $walletSentModel->transaction_id = $resultSent['hash'];
            $walletSentModel->response_data = json_encode($resultSent['responseData']);
            $walletSentModel->status = BWalletSent::$STATUS_SUCCESS;
        } else {
            $walletSentModel->response_data = is_array($resWalletSent->msg) ? json_encode($resWalletSent->msg) : $resWalletSent->msg;
            $walletSentModel->status = BWalletSent::$STATUS_FAIL;
        }
        if (!$walletSentModel->save()) {
            return new ReturnInfo(1, '激活失败', $walletSentModel->getFirstErrors());
        }
        return new ReturnInfo(0, '激活成功', $walletSentModel);
    }


    /**
     * 支付历史
     * @param string|null $userAddress
     * @param string|null $type
     * @return ReturnInfo
     */
    public function queryPayments(string $userAddress, string $page = "1", string $pageSize = "10")
    {
        $result = $this->send([], \Yii::$app->params['JTBusinessUrl'] . '/accounts/' . $userAddress . '/payments?results_per_page=' . $pageSize . '&page='.$page, 'get');
        if ($result->code === 0) {
            return new ReturnInfo(0, '', $result->content);
        }
        return $result;
    }

    
    /**
     * 拉取井通交易记录
     *
     * @param string $address
     * @param Int $page
     * @param Int $pageSize
     * @param boolean $is_update 是否立即更新记录状态
     * @return array [
     *  'address' => '钱包地址',
     *  'page' => '执行页码',
     *  'already' => '已存在记录hash以及类型',
     *  'status' => '拉取综合状态', # 只要有数据以及已存在记录 都返回 true，反之
     *  'list' => '记录详情',
     *  'count' => '记录详情条数'
     * ]
     */
    public function pullTransRecord(string $address = null, Int $page = 1, Int $pageSize = 10, bool $isUpdate = true) :array
    {
        $this->pullReponse['address'] = $address;
        $this->pullReponse['page'] = $page;
        $this->pullReponse['already'] = null;
        if ($page <= 1) {
            $this->pullReponse['count'] = 0;
            $this->pullReponse['status'] = false;
            $this->pullReponse['list'] = [];
        } else {
            $this->pullReponse['status'] = true;
        }
        if (is_null($address)) {
            return false;
        }
        $resJingTum = $this->queryPayments($address, $page, $pageSize);
        $transList = [];
        if ($resJingTum->code == 0 && !empty($resJingTum->content["payments"])) {
            $transList = $resJingTum->content["payments"];
        }
        $hasExist = false;
        foreach ($transList as $trans) {
            $this->pullReponse['count']++;
            $row['type'] = $trans['type'];
            $row['hash'] = $trans['hash'];
            $row['already'] = false;
            if ($trans['type'] == "received") {
                $source_address = $trans['counterparty'];
                $destination_address = $address;
            } else {
                $source_address = $address;
                $destination_address = $trans['counterparty'];
            }
            //记录是否存在
            if (BReportJingtum::find()->where(['hash' => $trans['hash'], 'destination_address' => $destination_address, 'type' => $trans['type']])->one()) {
                //后续的记录都存在，跳出循环，结束抓取
                $hasExist = $this->pullReponse['status'] = $row['already'] = true;
                $this->pullReponse['already'] = $trans['type'] .'-'. $trans['hash'];
                $this->pullReponse['list'][] = $row;
                break;
            }
            //添加记录
            $newReport = new BReportJingtum();
            $newReport->currency = $trans['amount']['currency'];
            $newReport->source_address = $source_address;
            $newReport->destination_address = $destination_address;
            $newReport->type = $trans['type'];
            $newReport->amount = $trans['amount']['value'];
            $newReport->fee = empty($trans['fee']) ? 0 : $trans['fee'];
            $newReport->hash = $trans['hash'];
            $newReport->date = $trans['date'];
            $newReport->result = $trans['result'];
            $newReport->raw_data = json_encode($trans);
            $newReport->create_time = NOW_TIME;
            $newReport->update_time = NOW_TIME;
            if ($newReport->insert() && $isUpdate) {
                $row['update_msg'] = $this->updateTrans($newReport)->msg;
            }
            $hasExist = null;
            $this->pullReponse['list'][] = $row;
        }
        return is_null($hasExist) ? $this->pullTransRecord($address, $page++, $pageSize, $isUpdate) : $this->pullReponse;
    }
    /**
     * 执行充值以及更新记录状态
     *
     * @param BReportJingtum $report
     * @return void
     */
    public function updateTrans(BReportJingtum $report)
    {
        $reponse = new ReturnInfo(1);
        $updateData = ['is_update' => BReportJingtum::$IS_UPDATE_YES, 'update_time' => NOW_TIME];
        if ($report->type == "received") {
            //充币收入
            $currency = BCurrency::find()->where(['code' => strtolower($report->currency)])->one();
            if (empty($currency)) {
                $reponse->msg = "no currency ".$report->currency;
                $updateData['is_update'] = BReportJingtum::$IS_UPDATE_NO;
            } elseif ($currency->recharge_status == BCurrency::$RECHARGE_STATUS_ON && $report->amount >= $currency->recharge_min_amount) {
                //可充币,符合最小入账数量

                //符合确认数，执行充值
                $res = RechargeService::handle($currency->id, $report->destination_address, "", $report->amount, $report->hash);

                if ($res->code == 0) {
                    //充值处理成功，停止更新
                    $updateData['is_update'] = BReportJingtum::$IS_UPDATE_NO;
                    $updateData['is_handle'] = BReportJingtum::$IS_HANDLE_RECHARGE;
                    $reponse->msg = "recharge success ";
                    $reponse->code = $res->code;
                } elseif ($res->code == 2) {
                    //状态异常，停止更新
                    $reponse->msg = "handle code error ".$res->msg;
                    $reponse->code = $res->code;
                    $updateData['is_update'] = BReportJingtum::$IS_UPDATE_NO;
                }
            } else {
                //不可充币，或不符合最小入账数量，停止更新
                if ($currency->recharge_status != BCurrency::$RECHARGE_STATUS_ON) {
                    $reponse->msg = "recharge_status ".$currency->recharge_status." != ".BCurrency::$RECHARGE_STATUS_ON;
                }
                if ($report->amount < $currency->recharge_min_amount) {
                    $reponse->msg = "recharge_min_amount ".$currency->recharge_min_amount." > ".$report->amount;
                }
                $updateData['is_update'] = BReportJingtum::$IS_UPDATE_NO;
            }
        } elseif ($report->type == 'sent') {
            //提现支出
            $reponse->msg = "type ".$report->type;
            //todo，停止更新
            $updateData['is_update'] = BReportJingtum::$IS_UPDATE_NO;
        } else {
            //其他，停止更新
            $reponse->msg = "type ".$report->type;
            $updateData['is_update'] = BReportJingtum::$IS_UPDATE_NO;
        }
        $reponse->content = $report;
        //修改更新状态，是否继续更新，默认继续更新
        BReportJingtum::updateAll($updateData, ['id' => $report->id]);
        return $reponse;
    }
}
