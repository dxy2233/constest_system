<?php

namespace common\services;

use common\components\FuncHelper;
use common\components\FuncResult;
use common\models\business\BSmsTemplate;
use common\models\SmsLog;

include ROOT . '/thirdpart/Dysmsapi/Request/V20170525/SendSmsRequest.php';

class SmsService extends ServiceBase
{
    public static function getInstance(): SmsService
    {
        self::$instance = new SmsService();
        self::$instance->init();

        return self::$instance;
    }



    /**
     * 发送短信
     *
     * @param $mobile
     * @param $params
     * @param $templateType
     * @param int $userId
     * @return FuncResult
     */
    public static function send($mobile, $params, $templateType, $userId = 0)
    {
        $content = BSmsTemplate::assembleContent($templateType, $params);
        $content = "【".\Yii::$app->params['smsSig']."】".$content;

        //存入短信日志
        $sms = new SmsLog();
        $sms->mobile = $mobile;
        $sms->content = $content;
        $sms->user_id = $userId;
        $sms->create_time = time();
        $sms->ret_code = '0';
        $sms->insert();

        //开关控制，特殊环境可不实际发送
        if (!isset(\Yii::$app->params['sendSms']) || \Yii::$app->params['sendSms'] === false) {
            return new FuncResult(0, null, $content);
        }

        //实际发送
        if(isset(\Yii::$app->params['smsType']) && \Yii::$app->params['smsType'] === "aliyun") {
            $sendResult = self::realSend($mobile, $params, $templateType);
            $sms->ret_code = $sendResult->msg;
        } else if(isset(\Yii::$app->params['smsType']) && \Yii::$app->params['smsType'] === "cl") {
            $sendResult = self::clSend($mobile, $content);
            $sms->ret_code = $sendResult->msg ? $sendResult->msg : $sendResult->content;
        } else {
            $sendResult = new FuncResult(1, 'no sms service');
            $sms->ret_code = $sendResult->msg;
        }

        $sms->send_time = time();
        $sms->update();

        return $sendResult;
    }

    /**
     * 创蓝253发送短信
     * @param $mobile
     * @param $content
     * @return FuncResult
     */
    public static function clSend($mobile, $content)
    {
        $count = preg_match('/^1\d{10}$/', $mobile);
        //国内电话自动加86
        if($count == 1) {
            $mobile = '86'.$mobile;
        }

        $url = 'http://intapi.253.com/send/json';
        $post = array(
            'account'  => \Yii::$app->params['smsKey'],
            'password' => \Yii::$app->params['smsSecret'],
            'msg' => $content,
            'mobile' => $mobile
        );

        $res = FuncHelper::curlPost($url, $post);

        $res = json_decode($res, true);

        return new FuncResult($res['code'], $res['error'], $res['msgid']);
    }


    /**
     * 实际发送短息
     * @param $mobile
     * @param $params
     * @param $templateType
     * @return FuncResult
     */
    public static function realSend($mobile, $params, $templateType)
    {
        $where = ['type' => $templateType];

        $smsTemplate = BSmsTemplate::findOne($where);

        return self::aliyunSend($mobile, $params, $smsTemplate->outer_template_id);
    }




    /**
     * 阿里云发送短信
     *
     * @param $mobile 手机号 多个使用,号分隔
     * @param $params 参数 对应模板变量
     * @param $templateId 对应阿里模板号
     * @return FuncResult 返回对象
     */
    private static function aliyunSend($mobile, $params, $templateId)
    {
        foreach ($params as &$param) {
            $param = (string)$param;
        }
        if (isset($params['vcode'])) {
            $params['code'] = $params['vcode'];
        }



        $accessKeyId = \Yii::$app->params['smsKey'];
        $accessKeySecret = \Yii::$app->params['smsSecret'];
        //短信API产品名
        $product = "Dysmsapi";
        //短信API产品域名
        $domain = "dysmsapi.aliyuncs.com";
        //暂时不支持多Region
        $region = "cn-hangzhou";
        //初始化访问的acsCleint
        $profile = \DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
        \DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
        $acsClient= new \DefaultAcsClient($profile);
        $request = new \Dysmsapi\Request\V20170525\SendSmsRequest;
        //必填-短信接收号码。支持以逗号分隔的形式进行批量调用，批量上限为20个手机号码,批量调用相对于单条调用及时性稍有延迟,验证码类型的短信推荐使用单条调用的方式
        $request->setPhoneNumbers($mobile);
        //必填-短信签名
        $request->setSignName(\Yii::$app->params['smsSig']);
        //必填-短信模板Code
        $request->setTemplateCode($templateId);
        //选填-假如模板中存在变量需要替换则为必填(JSON格式)
        $request->setTemplateParam(json_encode($params));
        //发起访问请求
        $acsResponse = $acsClient->getAcsResponse($request);
        if ($acsResponse->Message == 'OK') {
            return new FuncResult(0, $acsResponse->Code);
        } else {
            return new FuncResult(1, $acsResponse->Message . $acsResponse->Code, $acsResponse->RequestId);
        }
    }
}
