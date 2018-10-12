<?php
/**
 * Created by PhpStorm.
 * User: havoe
 * Date: 2017/7/18
 * Time: 下午5:19
 */

namespace common\services;

use common\services\ServiceBase;
use yii\helpers\ArrayHelper;


class JingTumService extends ServiceBase
{

    //贵人通
    const ASSETS_TYPE_GRT = 'GRT';

    //贵分
    const ASSETS_TYPE_GFC = 'GFC';



    protected $api_url;


    public function init() {
        parent::init();

        $this->api_url = \Yii::$app->params['JTBusinessUrl'];
    }


    public static function getInstance() : JingTumService {
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

        LogService::info('发起井通请求 - 请求数据 - ' . var_export($data, true) . ' - 返回数据 -' . var_export($sendResult, true), 'service');
        if ($sendResult->isOk) {
            $result = $sendResult->getData();
            if ($result['success']) {
                return new ReturnInfo(0, '请求成功', $result);
            }


            return new ReturnInfo(1, $result,$data);
        }


        return new ReturnInfo(1, '请求失败!',$data);


    }



    /**
     * 账户资产信息
     * @param string $key 公钥（地址）
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
    public function userTransferUser(string $userPrivateKey, string $userAddress,
                                     string $toUserAddress,string $outNo,
                                     float $value, string $remark, string $type)
    {

        $issuer = '';
        if($type != self::ASSETS_TYPE_GRT){
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
        if($type != self::ASSETS_TYPE_GRT){
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
        if($type !=self::ASSETS_TYPE_GRT){
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

}