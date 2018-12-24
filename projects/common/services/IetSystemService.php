<?php
namespace common\services;

use yii\base\Exception;
use yii\httpclient\Client;
use common\components\FuncHelper;

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
      'cusIdentity_sync' => '/customer/uip/cusIdentity/sync',
      // 节点新增
      'totalAmount_add' => '/customer/uip/totalAmount/add',
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
    public static function push(string $url, array $data)
    {
        $data['sign'] = self::MakeSign($data);
        // 自定义方法
        $response = FuncHelper::curlPost(self::$config['url'] . $url, $data);
        $response = json_decode($response);
        return new ReturnInfo($response->code, $response->msg, $response->success);
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
        if ($response->isOk) {
            return new ReturnInfo($response->data['code'], $response->data['msg'], $response->data['success']);
        }
        return new ReturnInfo(0, '推送失败');
        // $response = FuncHelper::curlPost(self::$config['url'], $data);
        print_r($response);
        exit;
    }
}
