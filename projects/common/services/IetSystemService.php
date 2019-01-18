<?php
namespace common\services;

use yii\base\Exception;
use yii\httpclient\Client;
use common\components\FuncHelper;
use common\models\business\BIetPush;
use common\models\business\BIetPushLog;

class IetSystemService extends ServiceBase
{
    /**
     * IET 接口列表
     */
    const IET_URL = [
      // 微店节点升级
      'wd_upgrade' => '/customer/uip/wd/upgrade',
      // 节点升级
      'node_upgrade' => '/customer/uip/node/upgrade',
      // 用户节点同步
      'cusIdentity_sync' => '/customer/uip/custidentity/sync',
      // 节点新增
      'totalAmount_add' => '/customer/uip/totalAmount/add',
      // 节点身份变更
      'identity_change' => '/customer/uip/custidentity/change',
      // 节点关系变更
      'recommend_change' => '/customer/uip/custidentity/inviterelation/change.json'
    ];

    protected static $config = [];
    protected static function GetConfig()
    {
        if (isset(\Yii::$app->params['ietApiConfig'])) {
            return self::$config = \Yii::$app->params['ietApiConfig'];
        } else {
            throw new Exception("配置不存在");
            return '签名类型不支持！';
        }
    }
    
    
    

    /**
     * 生成签名
    */
    protected static function MakeSign(array $values) :string
    {
        self::GetConfig();
        //签名步骤一：按字典序排序参数
        ksort($values);
        $string = self::ToUrlParams($values);

        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".self::$config['key'];

        //签名步骤三：MD5加密
        if (self::$config['signType'] == "MD5") {
            $string = md5($string);
        } else {
            throw new Exception("签名类型不支持！");
        }
        
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 格式化参数格式化成url参数
     */
    public static function ToUrlParams(array $values) :string
    {
        $buff = "";
        foreach ($values as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 数据推送
     *
        $data = [
            'phone' => '18584565115',
            'identity' => 2,
        ];
        var_dump($data, IetSystemService::push(IetSystemService::IET_URL['wd_upgrade'], $data));
     * @param array $data
     * @return void
     */
    public static function push(string $url, array $data, int $id)
    {
        //echo json_encode($data);
        foreach ($data as $k => $v) {
            if ($v === '') {
                unset($data[$k]);
            }
        }
        $data['sign'] = self::MakeSign($data);
        // // 自定义方法
        // $response = FuncHelper::curlPost(self::$config['url'] . $url, $data);
        // $response = json_decode($response);
        // self::createLog($url, $data, $response);
        // return new ReturnInfo($response->code, $response->msg, $response->success);
        // 系统方法
        $client = new Client(['baseUrl' => self::$config['url']]);
        $response = $client->createRequest()
        ->setFormat(Client::FORMAT_JSON)
        ->setMethod('POST')
        ->setUrl($url)
        ->setData($data)
        ->setOptions([
          'timeout' => 5, // set timeout to 5 seconds for the case server is not responding
        ])->send();
        self::createPushLog($url, $data, $response, $id);

        if ($response->isOk) {
            return new ReturnInfo($response->data['code'], $response->data['msg'], $response->data['success']);
        }
        return new ReturnInfo(0, '推送失败');
        // $response = FuncHelper::curlPost(self::$config['url'], $data);
        // print_r($response);
        // exit;
    }


    /**
     * 记录日志
     *
     * @param array $data
     * @return void
     */
    public static function createLog(string $url, array $data, $response = '')
    {
        $iet_push = new BIetPush();
        $iet_push->push_type = array_search($url, self::IET_URL);
        $iet_push->push_name = array_search($url, self::IET_URL);
        $iet_push->push_data = json_encode($data);
        if($response != ''){
            $iet_push->response = json_encode($response);
        }
        $iet_push->status = BIetPush::TENURE_WAIT;

        $iet_push->save();
    }
        /**
     * 记录日志
     *
     * @param array $data
     * @return void
     */
    public static function createPushLog(string $url, array $data, $response = '', $id = 0)
    {
        $iet_push = new BIetPushLog();
        $iet_push->push_type = array_search($url, self::IET_URL);
        $iet_push->push_data = json_encode($data);
        if($response != ''){
            $iet_push->response = json_encode($response->data);
        }
        $iet_push->create_time = time();
        $iet_push->relate_id = $id;
        if(!isset($response->data['code'])){
            var_dump($response->data);
            exit;
        }
        $iet_push->status = ($response->data['code'] == 0 || $response->data['code'] == 39606 || $response->data['code'] == 39513) ? 1 : 2;
        $iet_push->save();
    }
}
